<?php
namespace Procure\Domain\GoodsReceipt;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\Translator;
use Procure\Application\Command\GR\Options\GrRowCreateOptions;
use Procure\Application\Command\GR\Options\GrRowUpdateOptions;
use Procure\Application\DTO\Gr\GrDetailsDTO;
use Procure\Domain\Event\Gr\GrPosted;
use Procure\Domain\Event\Gr\GrReversed;
use Procure\Domain\Event\Gr\GrRowAdded;
use Procure\Domain\Event\Gr\GrRowUpdated;
use Procure\Domain\Exception\Gr\GrInvalidArgumentException;
use Procure\Domain\Exception\Gr\GrInvalidOperationException;
use Procure\Domain\Exception\Gr\GrPostingException;
use Procure\Domain\Exception\Gr\GrRowCreateException;
use Procure\Domain\Exception\Gr\GrRowUpdateException;
use Procure\Domain\Service\GrPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Shared\ProcureDocStatus;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Ramsey\Uuid\Uuid;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericGR extends AbstractGR
{

    abstract protected function prePost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService);

    abstract protected function doPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService);

    abstract protected function afterPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService);

    abstract protected function raiseEvent();

    abstract protected function preReserve(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService);

    abstract protected function doReverse(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService);

    abstract protected function afterReserve(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService);

    /**
     *
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param boolean $isPosting
     * @throws GrInvalidArgumentException
     * @return \Procure\Domain\GoodsReceipt\GenericGR
     */
    public function validate(HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, $isPosting = false)
    {
        if (! $headerValidators instanceof HeaderValidatorCollection) {
            throw new GrInvalidArgumentException("GR Validators not given!");
        }

        if (! $rowValidators instanceof RowValidatorCollection) {
            throw new GrInvalidArgumentException("GR Validators not given!");
        }

        // Clear Notification.
        $this->clearNotification();

        $this->validateHeader($headerValidators, $isPosting);

        if ($this->hasErrors()) {
            return $this;
        }

        if (count($this->getDocRows()) == 0) {
            $this->addError("Documment is empty. Please add line!");
            return $this;
        }

        foreach ($this->getDocRows() as $row) {
            $this->validateRow($row, $rowValidators, $isPosting);
        }

        return $this;
    }

    public function deactivateRow(GRRow $row, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService)
    {}

    /**
     *
     * @param GRRowSnapshot $snapshot
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param GrPostingService $postingService
     * @throws GrInvalidOperationException
     * @throws GrInvalidArgumentException
     * @throws GrRowUpdateException
     * @return \Procure\Domain\GoodsReceipt\GRRowSnapshot
     */
    public function createRowFrom(GRRowSnapshot $snapshot, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService)
    {
        if ($this->getDocStatus() == GRDocStatus::DOC_STATUS_POSTED) {
            throw new GrInvalidOperationException(sprintf("GR is posted! %s", $this->getId()));
        }

        if ($snapshot == null) {
            throw new GrInvalidArgumentException("PORowSnapshot not found");
        }

        $this->_checkParams($headerValidators, $rowValidators, $sharedService, $postingService);

        $createdDate = new \Datetime();
        $snapshot->createdOn = date_format($createdDate, 'Y-m-d H:i:s');
        $snapshot->quantity = $snapshot->docQuantity;
        $snapshot->revisionNo = 0;

        if ($snapshot->token == null) {
            $snapshot->token = Uuid::uuid4()->toString();
            $snapshot->uuid = $snapshot->getToken();
        }

        $snapshot->docStatus = ProcureDocStatus::DOC_STATUS_DRAFT;
        $snapshot->isActive = 1;
        $snapshot->docType = $this->docType;
        $snapshot->isDraft = 1;
        $snapshot->unitPrice = $snapshot->getDocUnitPrice();
        $snapshot->unit = $snapshot->getDocUnit();

        $row = GRRow::makeFromSnapshot($snapshot);

        $this->validateRow($row, $rowValidators);

        if ($this->hasErrors()) {
            throw new GrRowUpdateException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var GRRowSnapshot $localSnapshot
         */
        $localSnapshot = $postingService->getCmdRepository()->storeRow($this, $row);

        if ($localSnapshot == null) {
            throw new GrRowCreateException(sprintf("Error occured when creating row #%s", $this->getId()));
        }

        $trigger = null;
        if ($options instanceof GrRowCreateOptions) {
            $trigger = $options->getTriggeredBy();
        }

        $params = [
            "rowId" => $localSnapshot->getId(),
            "rowToken" => $localSnapshot->getToken()
        ];

        $this->addEvent(new GrRowAdded($this->getId(), $trigger, $params));

        return $localSnapshot;
    }

    /**
     *
     * @param GRRowSnapshot $snapshot
     * @param \Application\Domain\Shared\Command\CommandOptions $options
     * @param array $params
     * @param \Procure\Domain\Validator\HeaderValidatorCollection $headerValidators
     * @param \Procure\Domain\Validator\RowValidatorCollection $rowValidators
     * @param \Procure\Domain\Service\SharedService $sharedService
     * @param \Procure\Domain\Service\GrPostingService $postingService
     * @throws \Procure\Domain\Exception\Gr\GrInvalidOperationException
     * @throws \Procure\Domain\Exception\Gr\GrInvalidArgumentException
     * @throws \Procure\Domain\Exception\Gr\GrRowUpdateException
     * @return \Procure\Domain\GoodsReceipt\GRRowSnapshot
     */
    public function updateRowFrom(GRRowSnapshot $snapshot, CommandOptions $options, $params, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService)
    {
        if ($this->getDocStatus() == GRDocStatus::DOC_STATUS_POSTED) {
            throw new GrInvalidOperationException(sprintf("PO is posted! %s", $this->getId()));
        }

        if ($snapshot == null) {
            throw new GrInvalidArgumentException("PORowSnapshot not found");
        }

        $this->_checkParams($headerValidators, $rowValidators, $sharedService, $postingService);

        $createdDate = new \Datetime();
        $snapshot->lastchangeOn = date_format($createdDate, 'Y-m-d H:i:s');
        $snapshot->quantity = $snapshot->docQuantity;

        if ($snapshot->token == null) {
            $snapshot->token = Uuid::uuid4()->toString();
        }

        $snapshot->docType = $this->docType;
        $snapshot->isDraft = 1;
        $snapshot->unitPrice = $snapshot->getDocUnitPrice();
        $snapshot->unit = $snapshot->getDocUnit();

        $row = GrRow::makeFromSnapshot($snapshot);

        $this->validateRow($row, $rowValidators);

        if ($this->hasErrors()) {
            throw new GrRowUpdateException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var GRRowSnapshot $localSnapshot
         */
        $localSnapshot = $postingService->getCmdRepository()->storeRow($this, $row);

        if ($localSnapshot == null) {
            throw new GrRowUpdateException(sprintf("Error occured when creating GR Row #%s", $this->getId()));
        }

        $trigger = null;
        if ($options instanceof GrRowUpdateOptions) {
            $trigger = $options->getTriggeredBy();
        }

        $this->addEvent(new GrRowUpdated($this->getId(), $trigger, $params));

        return $localSnapshot;
    }

    /**
     *
     * @param \Application\Domain\Shared\Command\CommandOptions $options
     * @param \Procure\Domain\Validator\HeaderValidatorCollection $headerValidators
     * @param \Procure\Domain\Validator\RowValidatorCollection $rowValidators
     * @param \Procure\Domain\Service\SharedService $sharedService
     * @param \Procure\Domain\Service\GrPostingService $postingService
     * @throws \Procure\Domain\Exception\Gr\GrInvalidOperationException
     * @throws \Procure\Domain\Exception\Gr\GrPostingException
     * @return \Procure\Domain\GoodsReceipt\GenericGR
     */
    public function post(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService)
    {
        if ($this->getDocStatus() !== ProcureDocStatus::DOC_STATUS_DRAFT) {
            throw new GrInvalidOperationException(Translator::translate(sprintf("Document is already posted/closed or being amended! %s", __METHOD__)));
        }

        $this->_checkParams($headerValidators, $rowValidators, $sharedService, $postingService);

        $this->validate($headerValidators, $rowValidators);
        if ($this->hasErrors()) {
            throw new GrPostingException($this->getErrorMessage());
        }

        $this->clearEvents();

        $this->prePost($options, $headerValidators, $rowValidators, $sharedService, $postingService);
        $this->doPost($options, $headerValidators, $rowValidators, $sharedService, $postingService);
        $this->afterPost($options, $headerValidators, $rowValidators, $sharedService, $postingService);

        $this->addEvent(new GrPosted($this->makeSnapshot()));
        return $this;
    }

    /**
     *
     * @param \Application\Domain\Shared\Command\CommandOptions $options
     * @param \Procure\Domain\Validator\HeaderValidatorCollection $headerValidators
     * @param \Procure\Domain\Validator\RowValidatorCollection $rowValidators
     * @param \Procure\Domain\Service\SharedService $sharedService
     * @param \Procure\Domain\Service\GrPostingService $postingService
     * @throws \Procure\Domain\Exception\Gr\GrInvalidOperationException
     * @throws \Procure\Domain\Exception\Gr\GrPostingException
     * @return \Procure\Domain\GoodsReceipt\GenericGR
     */
    public function reverse(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService)
    {
        if ($this->getDocStatus() !== ProcureDocStatus::DOC_STATUS_POSTED) {
            throw new GrInvalidOperationException(Translator::translate(sprintf("Document is not posted yet! %s", __METHOD__)));
        }

        $this->_checkParams($headerValidators, $rowValidators, $sharedService, $postingService);

        $this->validate($headerValidators, $rowValidators);
        if ($this->hasErrors()) {
            throw new GrPostingException($this->getErrorMessage());
        }

        $this->clearEvents();

        $this->preReserve($options, $headerValidators, $rowValidators, $sharedService, $postingService);
        $this->doReverse($options, $headerValidators, $rowValidators, $sharedService, $postingService);
        $this->afterReserve($options, $headerValidators, $rowValidators, $sharedService, $postingService);

        $this->addEvent(new GrReversed($this->makeSnapshot()));
        return $this;
    }

    /**
     *
     * @param HeaderValidatorCollection $headerValidators
     * @param boolean $isPosting
     */
    public function validateHeader(HeaderValidatorCollection $headerValidators, $isPosting = false)
    {
        if (! $headerValidators instanceof HeaderValidatorCollection) {
            throw new GrInvalidArgumentException("GR Validators not given!");
        }

        $headerValidators->validate($this);
    }

    /**
     *
     * @param GrRow $row
     * @param RowValidatorCollection $rowValidators
     * @param boolean $isPosting
     * @throws GrInvalidArgumentException
     */
    public function validateRow(GrRow $row, RowValidatorCollection $rowValidators, $isPosting = false)
    {
        if (! $row instanceof GrRow) {
            throw new GrInvalidArgumentException("GR Row not given!");
        }

        if (! $rowValidators instanceof RowValidatorCollection) {
            throw new GrInvalidArgumentException("Row Validator not given!");
        }

        $rowValidators->validate($this, $row);

        if ($row->hasErrors()) {
            $this->addErrorArray($row->getErrors());
        } else {
            $row->refresh();
        }
    }

    /**
     *
     * @return NULL|object
     */
    public function makeDetailsDTO()
    {
        $dto = new GrDetailsDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @return NULL|object
     */
    public function makeHeaderDTO()
    {
        $dto = new GrDetailsDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @param object $dto
     * @return NULL|object
     */
    public function makeDTOForGrid($dto)
    {
        $dto = DTOFactory::createDTOFrom($this, $dto);
        $rowDTOList = [];
        if (count($this->docRows) > 0) {
            foreach ($this->docRows as $row) {

                if ($row instanceof GRRow) {
                    $rowDTOList[] = $row->makeDetailsDTO();
                }
            }
        }

        $dto->docRowsDTO = $rowDTOList;
        return $dto;
    }

    /**
     *
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param GrPostingService $postingService
     * @throws GrInvalidArgumentException
     */
    private function _checkParams(HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService)
    {
        if ($headerValidators == null) {
            throw new GrInvalidArgumentException("HeaderValidatorCollection not found");
        }

        if ($rowValidators == null) {
            throw new GrInvalidArgumentException("HeaderValidatorCollection not found");
        }

        if ($sharedService == null) {
            throw new GrInvalidArgumentException("SharedService service not found");
        }

        if ($postingService == null) {
            throw new GrInvalidArgumentException("postingService service not found");
        }
    }
}
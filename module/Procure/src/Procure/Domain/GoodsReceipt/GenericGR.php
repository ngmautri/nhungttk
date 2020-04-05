<?php
namespace Procure\Domain\GoodsReceipt;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Application\Command\PO\Options\PoRowCreateOptions;
use Procure\Application\DTO\Gr\GrDetailsDTO;
use Procure\Domain\Event\Gr\GrPosted;
use Procure\Domain\Event\Gr\GrRowAdded;
use Procure\Domain\Event\Gr\GrRowUpdated;
use Procure\Domain\Event\Po\PoAmendmentAccepted;
use Procure\Domain\Exception\PoAmendmentException;
use Procure\Domain\Exception\PoPostingException;
use Procure\Domain\Exception\Gr\GrAmendmentException;
use Procure\Domain\Exception\Gr\GrInvalidArgumentException;
use Procure\Domain\Exception\Gr\GrInvalidOperationException;
use Procure\Domain\Exception\Gr\GrPostingException;
use Procure\Domain\Exception\Gr\GrRowUpdateException;
use Procure\Domain\Service\GrPostingService;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Shared\Constants;
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

    abstract protected function prePost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService);

    abstract protected function doPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService);

    abstract protected function afterPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService);

    abstract protected function raiseEvent();

    abstract protected function preReserve(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService);

    abstract protected function doReverse(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService);

    abstract protected function afterReserve(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService);

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

    public function deactivateRow(GRRow $row, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {}

    /**
     *
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param SharedService $sharedService
     * @param GrPostingService $postingService
     * @throws GrInvalidArgumentException
     * @throws GrAmendmentException
     * @throws PoAmendmentException
     * @return \Procure\Domain\GoodsReceipt\GenericGR
     */
    public function enableAmendment(CommandOptions $options, HeaderValidatorCollection $headerValidators, SharedService $sharedService, GrPostingService $postingService)
    {
        if ($this->getDocStatus() !== GRDocStatus::DOC_STATUS_POSTED) {
            throw new GrInvalidArgumentException(sprintf($this->translate("GR document can not be amended! %s"), $this->getId()));
        }

        if (! $headerValidators instanceof HeaderValidatorCollection) {
            throw new GrInvalidArgumentException($this->translate("Validators not found"));
        }
        if ($sharedService == null) {
            throw new GrInvalidArgumentException($this->translate("SharedService service not found"));
        }

        if ($postingService == null) {
            throw new GrInvalidArgumentException($this->translate("postingService service not found"));
        }

        if ($options == null) {
            throw new GrInvalidArgumentException($this->translate("command options not found"));
        }

        if ($this->getTransactionStatus() == Constants::TRANSACTION_STATUS_COMPLETED) {
            throw new GrInvalidArgumentException($this->translate("GR is completed"));
        }

        $createdDate = new \Datetime();
        $this->setLastchangeOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $this->setLastchangeBy($options->getUserId());
        $this->setDocVersion($this->getDocVersion() + 1);
        $this->setDocStatus(GRDocStatus::DOC_STATUS_AMENDING);

        $this->validateHeader($headerValidators);

        if ($this->hasErrors()) {
            throw new GrAmendmentException($this->getErrorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var GRSnapshot $rootSnapshot
         */
        $rootSnapshot = $postingService->getCmdRepository()->storeHeader($this, false);

        if ($rootSnapshot == null) {
            throw new GrAmendmentException(sprintf($this->translate("Error orcured when enabling GR for amendment #%s"), $this->getId()));
        }

        $this->id = $rootSnapshot->getId();
        $trigger = $options->getTriggeredBy();
        $params = null;

        $this->addEvent(new GrAmendmentException($rootSnapshot, $trigger, $params));
        return $this;
    }

    public function acceptAmendment(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {
        if ($this->getDocStatus() !== GrDocStatus::DOC_STATUS_AMENDING) {
            throw new GrInvalidOperationException(sprintf("Document is not on amendment! %s", $this->getId()));
        }

        if ($headerValidators == null) {
            throw new GrInvalidOperationException($this->translate("HeaderValidatorCollection not found"));
        }

        if ($rowValidators == null) {
            throw new GrInvalidOperationException($this->translate("HeaderValidatorCollection not found"));
        }

        if ($sharedService == null) {
            throw new GrInvalidOperationException($this->translate("SharedService service not found"));
        }

        if ($postingService == null) {
            throw new GrInvalidOperationException($this->translate("postingService service not found"));
        }

        if ($options == null) {
            throw new GrInvalidOperationException($this->translate("Comnand Options not found!"));
        }

        $createdDate = new \Datetime();
        $this->setLastchangeOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $this->setLastchangeBy($options->getUserId());
        $this->setDocStatus(GRDocStatus::DOC_STATUS_POSTED);

        $this->validate($headerValidators, $rowValidators);

        if ($this->hasErrors()) {
            throw new GrAmendmentException($this->getErrorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var GRSnapshot $rootSnapshot
         */
        $rootSnapshot = $postingService->getCmdRepository()->post($this, false);

        if ($rootSnapshot == null) {
            throw new GrAmendmentException(sprintf("Error orcured when posting amendment of PO #%s", $this->getId()));
        }

        $this->id = $rootSnapshot->getId();
        $trigger = $options->getTriggeredBy();
        $params = null;

        $this->addEvent(new PoAmendmentAccepted($rootSnapshot, $trigger, $params));
        return $this;
    }

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
            throw new GrInvalidOperationException(sprintf("PO is posted! %s", $this->getId()));
        }

        if ($snapshot == null) {
            throw new GrInvalidArgumentException("PORowSnapshot not found");
        }

        if ($headerValidators == null) {
            throw new GrInvalidArgumentException("HeaderValidatorCollection service not found");
        }

        if ($rowValidators == null) {
            throw new GrInvalidArgumentException("HeaderValidatorCollection service not found");
        }

        if ($sharedService == null) {
            throw new GrInvalidArgumentException("SharedService service not found");
        }

        if ($postingService == null) {
            throw new GrInvalidArgumentException("postingService service not found");
        }

        $createdDate = new \Datetime();
        $snapshot->createdOn = date_format($createdDate, 'Y-m-d H:i:s');
        $snapshot->quantity = $snapshot->docQuantity;
        $snapshot->revisionNo = 0;

        if ($snapshot->token == null) {
            $snapshot->token = Uuid::uuid4()->toString();
        }

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
            throw new GrRowUpdateException(sprintf("Error occured when creating PO Row #%s", $this->getId()));
        }

        $trigger = null;
        if ($options instanceof PoRowCreateOptions) {
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
     * @param CommandOptions $options
     * @param array $params
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param POPostingService $postingService
     * @throws GrInvalidOperationException
     * @throws GrInvalidArgumentException
     * @throws GrRowUpdateException
     * @return \Procure\Domain\GoodsReceipt\GRRowSnapshot
     */
    public function updateRowFrom(GRRowSnapshot $snapshot, CommandOptions $options, $params, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {
        if ($this->getDocStatus() == GRDocStatus::DOC_STATUS_POSTED) {
            throw new GrInvalidOperationException(sprintf("PO is posted! %s", $this->getId()));
        }

        if ($snapshot == null) {
            throw new GrInvalidArgumentException("PORowSnapshot not found");
        }

        if ($headerValidators == null) {
            throw new GrInvalidArgumentException("HeaderValidatorCollection service not found");
        }

        if ($rowValidators == null) {
            throw new GrInvalidArgumentException("HeaderValidatorCollection service not found");
        }

        if ($sharedService == null) {
            throw new GrInvalidArgumentException("SharedService service not found");
        }

        if ($postingService == null) {
            throw new GrInvalidArgumentException("postingService service not found");
        }

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
            throw new GrRowUpdateException(sprintf("Error occured when creating PO Row #%s", $this->getId()));
        }

        $trigger = null;
        if ($options instanceof PoRowCreateOptions) {
            $trigger = $options->getTriggeredBy();
        }

        $this->addEvent(new GrRowUpdated($this->getId(), $trigger, $params));

        return $localSnapshot;
    }

    /**
     *
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param GrPostingException $postingService
     * @throws GrInvalidOperationException
     * @throws GrInvalidArgumentException
     * @throws PoPostingException
     * @return \Procure\Domain\GoodsReceipt\GenericGR
     */
    public function post(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingException $postingService)
    {
        if (! $this->getDocStatus() == GRDocStatus::DOC_STATUS_DRAFT) {
            throw new GrInvalidOperationException(sprintf("PO is already posted/closed or being amended! %s", $this->getId()));
        }

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

        if ($options == null) {
            throw new GrInvalidArgumentException("Comnand Options not found!");
        }

        $this->validate($headerValidators, $rowValidators);
        if ($this->hasErrors()) {
            throw new PoPostingException($this->getErrorMessage());
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

        if (count($this->docRows) > 0) {
            foreach ($this->docRows as $row) {

                if ($row instanceof GrRow) {
                    $dto->docRowsDTO[] = $row->makeDetailsDTO();
                }
            }
        }
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
                    $rowDTOList[] = $row->makeDTOForGrid();
                }
            }
        }

        $dto->docRowsDTO = $rowDTOList;
        return $dto;
    }
  
}
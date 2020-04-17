<?php
namespace Procure\Domain\AccountPayable;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\Translator;
use Procure\Application\DTO\Ap\ApDetailsDTO;
use Procure\Domain\Event\Ap\ApPosted;
use Procure\Domain\Event\Ap\ApReversed;
use Procure\Domain\Event\Ap\ApRowAdded;
use Procure\Domain\Event\Ap\ApRowUpdated;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\InvalidOperationException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Service\APPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Shared\Constants;
use Procure\Domain\Shared\ProcureDocStatus;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Ramsey\Uuid\Uuid;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericAP extends AbstractAP
{

    abstract protected function prePost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, APPostingService $postingService);

    abstract protected function doPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, APPostingService $postingService);

    abstract protected function afterPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, APPostingService $postingService);

    abstract protected function raiseEvent();

    abstract protected function preReserve(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, APPostingService $postingService);

    abstract protected function doReverse(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, APPostingService $postingService);

    abstract protected function afterReserve(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, APPostingService $postingService);

    /**
     *
     * @param \Procure\Domain\Validator\HeaderValidatorCollection $headerValidators
     * @param \Procure\Domain\Validator\RowValidatorCollection $rowValidators
     * @param boolean $isPosting
     * @throws \Procure\Domain\Exception\InvalidArgumentException
     * @return \Procure\Domain\AccountPayable\GenericAP
     */
    public function validate(HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, $isPosting = false)
    {
        if (! $headerValidators instanceof HeaderValidatorCollection) {
            throw new InvalidArgumentException("Validators not given!");
        }

        if (! $rowValidators instanceof RowValidatorCollection) {
            throw new InvalidArgumentException("GR Validators not given!");
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

    public function deactivateRow(APRow $row, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, APPostingService $postingService)
    {}

    /**
     *
     * @param APRowSnapshot $snapshot
     * @param \Application\Domain\Shared\Command\CommandOptions $options
     * @param \Procure\Domain\Validator\HeaderValidatorCollection $headerValidators
     * @param \Procure\Domain\Validator\RowValidatorCollection $rowValidators
     * @param \Procure\Domain\Service\SharedService $sharedService
     * @param \Procure\Domain\Service\APPostingService $postingService
     * @throws \Procure\Domain\Exception\InvalidOperationException
     * @throws \Procure\Domain\Exception\InvalidArgumentException
     * @throws \Procure\Domain\Exception\OperationFailedException
     * @return \Procure\Domain\AccountPayable\APRowSnapshot
     */
    public function createRowFrom(APRowSnapshot $snapshot, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, APPostingService $postingService)
    {
        if ($this->getDocStatus() == Constants::DOC_STATUS_POSTED) {
            throw new InvalidOperationException(sprintf("AP is posted! %s", $this->getId()));
        }

        if ($snapshot == null) {
            throw new InvalidArgumentException("Row Snapshot not found");
        }

        if ($options == null) {
            throw new InvalidArgumentException("Options not found");
        }

        $this->_checkParams($headerValidators, $rowValidators, $sharedService, $postingService);

        $snapshot->docType = $this->docType;

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $snapshot->initSnapshot($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $row = APRow::makeFromSnapshot($snapshot);

        $this->validateRow($row, $rowValidators);

        if ($this->hasErrors()) {
            throw new OperationFailedException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var APRowSnapshot $localSnapshot
         */
        $localSnapshot = $postingService->getCmdRepository()->storeRow($this, $row);

        if ($localSnapshot == null) {
            throw new OperationFailedException(sprintf("Error occured when creating row #%s", $this->getId()));
        }

        $trigger = $options->getTriggeredBy();

        $params = [
            "rowId" => $localSnapshot->getId(),
            "rowToken" => $localSnapshot->getToken()
        ];

        $this->addEvent(new ApRowAdded($this->getId(), $trigger, $params));

        return $localSnapshot;
    }

    /**
     *
     * @param APRowSnapshot $snapshot
     * @param \Application\Domain\Shared\Command\CommandOptions $options
     * @param array $params
     * @param \Procure\Domain\Validator\HeaderValidatorCollection $headerValidators
     * @param \Procure\Domain\Validator\RowValidatorCollection $rowValidators
     * @param \Procure\Domain\Service\SharedService $sharedService
     * @param \Procure\Domain\Service\APPostingService $postingService
     * @throws \Procure\Domain\Exception\InvalidOperationException
     * @throws \Procure\Domain\Exception\InvalidArgumentException
     * @throws \Procure\Domain\Exception\OperationFailedException
     * @return \Procure\Domain\AccountPayable\APRowSnapshot
     */
    public function updateRowFrom(APRowSnapshot $snapshot, CommandOptions $options, $params, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, APPostingService $postingService)
    {
        if ($this->getDocStatus() == Constants::DOC_STATUS_POSTED) {
            throw new InvalidOperationException(sprintf("PO is posted! %s", $this->getId()));
        }

        if ($snapshot == null) {
            throw new InvalidArgumentException("APRowSnapshot not found");
        }

        if ($options == null) {
            throw new InvalidArgumentException("Options not found");
        }

        $this->_checkParams($headerValidators, $rowValidators, $sharedService, $postingService);

        $createdDate = new \Datetime();
        $snapshot->lastchangeOn = date_format($createdDate, 'Y-m-d H:i:s');
        $snapshot->lastchangeBy = $options->getUserId();
        $snapshot->revisionNo ++;
        $snapshot->quantity = $snapshot->docQuantity;

        if ($snapshot->token == null) {
            $snapshot->token = Uuid::uuid4()->toString();
        }

        $row = APRow::makeFromSnapshot($snapshot);

        $this->validateRow($row, $rowValidators);

        if ($this->hasErrors()) {
            throw new OperationFailedException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var APRowSnapshot $localSnapshot
         */
        $localSnapshot = $postingService->getCmdRepository()->storeRow($this, $row);

        if ($localSnapshot == null) {
            throw new OperationFailedException(sprintf("Error occured when creating GR Row #%s", $this->getId()));
        }

        $trigger = $options->getTriggeredBy();

        $this->addEvent(new ApRowUpdated($this->getId(), $trigger, $params));

        return $localSnapshot;
    }

    /**
     *
     * @param \Application\Domain\Shared\Command\CommandOptions $options
     * @param \Procure\Domain\Validator\HeaderValidatorCollection $headerValidators
     * @param \Procure\Domain\Validator\RowValidatorCollection $rowValidators
     * @param \Procure\Domain\Service\SharedService $sharedService
     * @param \Procure\Domain\Service\APPostingService $postingService
     * @throws \Procure\Domain\Exception\InvalidOperationException
     * @throws \Procure\Domain\Exception\OperationFailedException
     * @return \Procure\Domain\AccountPayable\GenericAP
     */
    public function post(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, APPostingService $postingService)
    {
        if ($this->getDocStatus() !== ProcureDocStatus::DOC_STATUS_DRAFT) {
            throw new InvalidOperationException(Translator::translate(sprintf("Document is already posted/closed or being amended! %s", __FUNCTION__)));
        }

        $this->_checkParams($headerValidators, $rowValidators, $sharedService, $postingService);

        $this->validate($headerValidators, $rowValidators);
        if ($this->hasErrors()) {
            throw new OperationFailedException($this->getErrorMessage());
        }

        $this->clearEvents();

        $this->prePost($options, $headerValidators, $rowValidators, $sharedService, $postingService);
        $this->doPost($options, $headerValidators, $rowValidators, $sharedService, $postingService);
        $this->afterPost($options, $headerValidators, $rowValidators, $sharedService, $postingService);

        $this->addEvent(new ApPosted($this->makeSnapshot()));
        return $this;
    }

    /**
     *
     * @param \Application\Domain\Shared\Command\CommandOptions $options
     * @param \Procure\Domain\Validator\HeaderValidatorCollection $headerValidators
     * @param \Procure\Domain\Validator\RowValidatorCollection $rowValidators
     * @param \Procure\Domain\Service\SharedService $sharedService
     * @param \Procure\Domain\Service\APPostingService $postingService
     * @throws \Procure\Domain\Exception\InvalidArgumentException
     * @throws \Procure\Domain\Exception\OperationFailedException
     * @return \Procure\Domain\AccountPayable\GenericAP
     */
    public function reverse(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, APPostingService $postingService)
    {
        if ($this->getDocStatus() !== ProcureDocStatus::DOC_STATUS_POSTED) {
            throw new InvalidArgumentException(Translator::translate(sprintf("Document is not posted yet! %s", __METHOD__)));
        }

        $this->_checkParams($headerValidators, $rowValidators, $sharedService, $postingService);

        $this->validate($headerValidators, $rowValidators);
        if ($this->hasErrors()) {
            throw new OperationFailedException($this->getErrorMessage());
        }

        $this->clearEvents();

        $this->preReserve($options, $headerValidators, $rowValidators, $sharedService, $postingService);
        $this->doReverse($options, $headerValidators, $rowValidators, $sharedService, $postingService);
        $this->afterReserve($options, $headerValidators, $rowValidators, $sharedService, $postingService);

        $this->addEvent(new ApReversed($this->makeSnapshot()));
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
            throw new InvalidArgumentException("Validators not given!");
        }

        $headerValidators->validate($this);
    }

    /**
     *
     * @param APRow $row
     * @param \Procure\Domain\Validator\RowValidatorCollection $rowValidators
     * @param boolean $isPosting
     * @throws \Procure\Domain\Exception\InvalidArgumentException
     */
    public function validateRow(APRow $row, RowValidatorCollection $rowValidators, $isPosting = false)
    {
        if (! $row instanceof APRow) {
            throw new InvalidArgumentException("GR Row not given!");
        }

        if (! $rowValidators instanceof RowValidatorCollection) {
            throw new InvalidArgumentException("Row Validator not given!");
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
        $dto = new ApDetailsDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @return NULL|object
     */
    public function makeHeaderDTO()
    {
        $dto = new ApDetailsDTO();
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

                if ($row instanceof APRow) {
                    $rowDTOList[] = $row->makeDTOForGrid();
                }
            }
        }

        $dto->docRowsDTO = $rowDTOList;
        return $dto;
    }

    /**
     *
     * @param \Procure\Domain\Validator\HeaderValidatorCollection $headerValidators
     * @param \Procure\Domain\Validator\RowValidatorCollection $rowValidators
     * @param \Procure\Domain\Service\SharedService $sharedService
     * @param \Procure\Domain\Service\APPostingService $postingService
     * @throws \Procure\Domain\Exception\InvalidArgumentException
     */
    private function _checkParams(HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, APPostingService $postingService)
    {
        if ($headerValidators == null) {
            throw new InvalidArgumentException("HeaderValidatorCollection not found");
        }

        if ($rowValidators == null) {
            throw new InvalidArgumentException("HeaderValidatorCollection not found");
        }

        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("postingService service not found");
        }
    }
}
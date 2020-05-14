<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Application\DTO\Po\PoDetailsDTO;
use Procure\Domain\Event\Po\PoAmendmentAccepted;
use Procure\Domain\Event\Po\PoAmendmentEnabled;
use Procure\Domain\Event\Po\PoPosted;
use Procure\Domain\Event\Po\PoRowAdded;
use Procure\Domain\Event\Po\PoRowUpdated;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Exception\PoAmendmentException;
use Procure\Domain\Exception\ValidationFailedException;
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
abstract class GenericPO extends BaseDoc
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
     * @param PORow $row
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param POPostingService $postingService
     */
    public function deactivateRow(PORow $row, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {}

    /**
     *
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param SharedService $sharedService
     * @param POPostingService $postingService
     */
    public function enableAmendment(CommandOptions $options, HeaderValidatorCollection $headerValidators, SharedService $sharedService, POPostingService $postingService)
    {
        if ($this->getDocStatus() !== PODocStatus::DOC_STATUS_POSTED) {
            throw new InvalidArgumentException(sprintf("PO can not be amended! %s", $this->getId()));
        }

        if (! $headerValidators instanceof HeaderValidatorCollection) {
            throw new InvalidArgumentException("Validators not found");
        }
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("postingService service not found");
        }

        if ($options == null) {
            throw new InvalidArgumentException("command options not found");
        }

        if ($this->getTransactionStatus() == Constants::TRANSACTION_STATUS_COMPLETED) {
            throw new InvalidArgumentException("PO is completed");
        }

        $createdDate = new \Datetime();
        $this->setLastchangeOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $this->setLastchangeBy($options->getUserId());
        $this->setDocVersion($this->getDocVersion() + 1);
        $this->setDocStatus(PODocStatus::DOC_STATUS_AMENDING);

        $this->validateHeader($headerValidators);

        if ($this->hasErrors()) {
            throw new PoAmendmentException($this->getErrorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var POSnapshot $rootSnapshot
         */
        $rootSnapshot = $postingService->getCmdRepository()->storeHeader($this, false);

        if ($rootSnapshot == null) {
            throw new PoAmendmentException(sprintf("Error orcured when enabling PO for amendment #%s", $this->getId()));
        }

        $this->id = $rootSnapshot->getId();

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetDocVersion($rootSnapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new PoAmendmentEnabled($target, $defaultParams, $params);
        $this->addEvent($event);
        return $this;
    }

    /**
     *
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param SharedService $sharedService
     * @param POPostingService $postingService
     * @throws InvalidArgumentException
     * @throws InvalidArgumentException
     */
    public function acceptAmendment(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {
        if ($this->getDocStatus() !== PODocStatus::DOC_STATUS_AMENDING) {
            throw new InvalidArgumentException(sprintf("Document is not on amendment! %s", $this->getId()));
        }

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

        if ($options == null) {
            throw new InvalidArgumentException("Comnand Options not found!");
        }

        $createdDate = new \Datetime();
        $this->setLastchangeOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $this->setLastchangeBy($options->getUserId());
        $this->setDocStatus(PODocStatus::DOC_STATUS_POSTED);

        $this->validate($headerValidators, $rowValidators);

        if ($this->hasErrors()) {
            throw new PoAmendmentException($this->getErrorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var POSnapshot $rootSnapshot
         */
        $rootSnapshot = $postingService->getCmdRepository()->post($this, false);

        if ($rootSnapshot == null) {
            throw new PoAmendmentException(sprintf("Error orcured when posting amendment of PO #%s", $this->getId()));
        }

        $this->id = $rootSnapshot->getId();

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetDocVersion($rootSnapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new PoAmendmentAccepted($target, $defaultParams, $params);
        $this->addEvent($event);

        $event = new PoPosted($target, $defaultParams, $params);
        $this->addEvent($event);

        return $this;
    }

    /**
     *
     * @param PORowSnapshot $snapshot
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $specService
     * @param SharedService $sharedService
     * @param POPostingService $postingService
     * @throws InvalidArgumentException
     * @throws OperationFailedException
     * @return \Procure\Domain\PurchaseOrder\PORowSnapshot
     */
    public function createRowFrom(PORowSnapshot $snapshot, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {
        if ($this->getDocStatus() == PODocStatus::DOC_STATUS_POSTED) {
            throw new InvalidArgumentException(sprintf("PO is posted! %s", $this->getId()));
        }

        if ($snapshot == null) {
            throw new InvalidArgumentException("PORowSnapshot not found");
        }

        if ($headerValidators == null) {
            throw new InvalidArgumentException("HeaderValidatorCollection service not found");
        }

        if ($rowValidators == null) {
            throw new InvalidArgumentException("HeaderValidatorCollection service not found");
        }

        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("postingService service not found");
        }

        if ($options == null) {
            throw new InvalidArgumentException("Option service not found");
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

        $row = PORow::makeFromSnapshot($snapshot);

        $this->validateRow($row, $rowValidators);

        if ($this->hasErrors()) {
            throw new OperationFailedException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var PORowSnapshot $localSnapshot
         */
        $localSnapshot = $postingService->getCmdRepository()->storeRow($this, $row);

        if ($localSnapshot == null) {
            throw new OperationFailedException(sprintf("Error occured when creating PO Row #%s", $this->getId()));
        }

        $params = [
            "rowId" => $localSnapshot->getId(),
            "rowToken" => $localSnapshot->getToken()
        ];

        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new PoRowAdded($this->makeSnapshot(), $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param PORowSnapshot $snapshot
     * @param CommandOptions $options
     * @param $params $params
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param POPostingService $postingService
     * @throws InvalidArgumentException
     * @throws OperationFailedException
     * @throws OperationFailedException
     * @return \Procure\Domain\PurchaseOrder\PORowSnapshot
     */
    public function updateRowFrom(PORowSnapshot $snapshot, CommandOptions $options, $params, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {
        if ($this->getDocStatus() == PODocStatus::DOC_STATUS_POSTED) {
            throw new InvalidArgumentException(sprintf("PO is posted! %s", $this->getId()));
        }

        if ($snapshot == null) {
            throw new InvalidArgumentException("PORowSnapshot not found");
        }

        if ($headerValidators == null) {
            throw new InvalidArgumentException("HeaderValidatorCollection service not found");
        }

        if ($rowValidators == null) {
            throw new InvalidArgumentException("HeaderValidatorCollection service not found");
        }

        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("postingService service not found");
        }

        if ($options == null) {
            throw new InvalidArgumentException("Opttions service not found");
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

        $row = PORow::makeFromSnapshot($snapshot);

        $this->validateRow($row, $rowValidators);

        if ($this->hasErrors()) {
            throw new OperationFailedException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var PORowSnapshot $localSnapshot
         */
        $localSnapshot = $postingService->getCmdRepository()->storeRow($this, $row);

        if ($localSnapshot == null) {
            throw new OperationFailedException(sprintf("Error occured when creating PO Row #%s", $this->getId()));
        }

        $target = $this->makeSnapshot();

        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new PoRowUpdated($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    /**
     *
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param POPostingService $postingService
     * @throws InvalidArgumentException
     * @return \Procure\Domain\PurchaseOrder\GenericPO
     */
    public function post(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {
        if (! $this->getDocStatus() == PODocStatus::DOC_STATUS_DRAFT) {
            throw new InvalidArgumentException(sprintf("PO is already posted/closed or being amended! %s", $this->getId()));
        }

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

        if ($options == null) {
            throw new InvalidArgumentException("Comnand Options not found!");
        }

        $this->validate($headerValidators, $rowValidators);
        if ($this->hasErrors()) {
            throw new ValidationFailedException($this->getErrorMessage());
        }

        $this->clearEvents();

        $this->prePost($options, $headerValidators, $rowValidators, $sharedService, $postingService);
        $this->doPost($options, $headerValidators, $rowValidators, $sharedService, $postingService);
        $this->afterPost($options, $headerValidators, $rowValidators, $sharedService, $postingService);

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        $defaultParams->setTargetDocVersion($this->getDocVersion());
        $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new PoPosted($target, $defaultParams, $params);
        $this->addEvent($event);
        return $this;
    }

    /**
     *
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     * @return \Procure\Domain\PurchaseOrder\GenericPO
     */
    public function validate(HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, $isPosting = false)
    {
        if (! $headerValidators instanceof HeaderValidatorCollection) {
            throw new InvalidArgumentException("PO Validators not given!");
        }

        if (! $rowValidators instanceof RowValidatorCollection) {
            throw new InvalidArgumentException("PO Validators not given!");
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

        foreach ($this->docRows as $row) {
            $this->validateRow($row, $rowValidators, $isPosting);
        }

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
            throw new InvalidArgumentException("PO Validators not given!");
        }

        $headerValidators->validate($this);
    }

    /**
     *
     * @param PORow $row
     * @param RowValidatorCollection $rowValidators
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     */
    public function validateRow(PORow $row, RowValidatorCollection $rowValidators, $isPosting = false)
    {
        if (! $row instanceof PORow) {
            throw new InvalidArgumentException("Po Row not given!");
        }

        if (! $rowValidators instanceof RowValidatorCollection) {
            throw new InvalidArgumentException("Row Validator not given!");
        }

        $rowValidators->validate($this, $row);

        if ($row->hasErrors()) {
            $this->addErrorArray($row->getErrors());
            return;
        }

        $row->calculate(); //
    }

    /**
     *
     * @return NULL|\Procure\Application\DTO\Po\PoDetailsDTO
     */
    public function makeDetailsDTO()
    {
        $dto = new PoDetailsDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);

        if (count($this->docRows) > 0) {
            foreach ($this->docRows as $row) {

                if ($row instanceof PORow) {
                    $dto->docRowsDTO[] = $row->makeDetailsDTO();
                }
            }
        }
        return $dto;
    }

    /**
     *
     * @return NULL|\Procure\Application\DTO\Po\PoDetailsDTO
     */
    public function makeHeaderDTO()
    {
        $dto = new PoDetailsDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @return NULL|\Procure\Application\DTO\Po\PoDetailsDTO
     */
    public function makeDTOForGrid()
    {
        $dto = new PoDetailsDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        $rowDTOList = [];
        if (count($this->docRows) > 0) {
            foreach ($this->docRows as $row) {

                if ($row instanceof PORow) {
                    $rowDTOList[] = $row->makeDTOForGrid();
                }
            }
        }

        $dto->docRowsDTO = $rowDTOList;
        return $dto;
    }

    /**
     *
     * @param mixed $rowsOutput
     */
    public function setRowsOutput($rowsOutput)
    {
        $this->rowsOutput = $rowsOutput;
    }
}
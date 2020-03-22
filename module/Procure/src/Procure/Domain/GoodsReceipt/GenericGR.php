<?php
namespace Procure\Domain\GoodsReceipt;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Application\Command\PO\Options\PoRowCreateOptions;
use Procure\Application\DTO\Po\PoDetailsDTO;
use Procure\Domain\APInvoice\Factory\APFactory;
use Procure\Domain\Event\Po\PoAmendmentAccepted;
use Procure\Domain\Event\Po\PoAmendmentEnabled;
use Procure\Domain\Event\Po\PoPosted;
use Procure\Domain\Event\Po\PoRowAdded;
use Procure\Domain\Event\Po\PoRowUpdated;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\PoAmendmentException;
use Procure\Domain\Exception\PoInvalidArgumentException;
use Procure\Domain\Exception\PoInvalidOperationException;
use Procure\Domain\Exception\PoPostingException;
use Procure\Domain\Exception\PoRowCreateException;
use Procure\Domain\Exception\PoRowException;
use Procure\Domain\PurchaseOrder\PODocStatus;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Domain\PurchaseOrder\PORowSnapshot;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Domain\PurchaseOrder\Validator\HeaderValidatorCollection;
use Procure\Domain\PurchaseOrder\Validator\RowValidatorCollection;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Shared\Constants;
use Ramsey\Uuid\Uuid;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericGR extends AbstractGR
{

    protected $docRows;

    protected $rowIdArray;

    protected $rowsOutput;

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
            throw new PoInvalidOperationException(sprintf("PO can not be amended! %s", $this->getId()));
        }

        if (! $headerValidators instanceof HeaderValidatorCollection) {
            throw new PoInvalidArgumentException("Validators not found");
        }
        if ($sharedService == null) {
            throw new PoInvalidArgumentException("SharedService service not found");
        }

        if ($postingService == null) {
            throw new PoInvalidArgumentException("postingService service not found");
        }

        if ($options == null) {
            throw new PoInvalidArgumentException("command options not found");
        }

        if ($this->getTransactionStatus() == Constants::TRANSACTION_STATUS_COMPLETED) {
            throw new PoInvalidArgumentException("PO is completed");
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
        $trigger = $options->getTriggeredBy();
        $params = null;

        $this->addEvent(new PoAmendmentEnabled($rootSnapshot, $trigger, $params));
        return $this;
    }

    /**
     *
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param SharedService $sharedService
     * @param POPostingService $postingService
     * @throws PoInvalidOperationException
     * @throws PoInvalidArgumentException
     */
    public function acceptAmendment(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {
        if ($this->getDocStatus() !== PODocStatus::DOC_STATUS_AMENDING) {
            throw new PoInvalidOperationException(sprintf("Document is not on amendment! %s", $this->getId()));
        }

        if ($headerValidators == null) {
            throw new PoInvalidArgumentException("HeaderValidatorCollection not found");
        }

        if ($rowValidators == null) {
            throw new PoInvalidArgumentException("HeaderValidatorCollection not found");
        }

        if ($sharedService == null) {
            throw new PoInvalidArgumentException("SharedService service not found");
        }

        if ($postingService == null) {
            throw new PoInvalidArgumentException("postingService service not found");
        }

        if ($options == null) {
            throw new PoInvalidArgumentException("Comnand Options not found!");
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
        $trigger = $options->getTriggeredBy();
        $params = null;

        $this->addEvent(new PoAmendmentAccepted($rootSnapshot, $trigger, $params));
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
     * @throws PoInvalidArgumentException
     * @throws PoRowException
     * @return \Procure\Domain\PurchaseOrder\PORowSnapshot
     */
    public function createRowFrom(PORowSnapshot $snapshot, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {
        if ($this->getDocStatus() == PODocStatus::DOC_STATUS_POSTED) {
            throw new PoInvalidOperationException(sprintf("PO is posted! %s", $this->getId()));
        }

        if ($snapshot == null) {
            throw new PoInvalidArgumentException("PORowSnapshot not found");
        }

        if ($headerValidators == null) {
            throw new PoInvalidArgumentException("HeaderValidatorCollection service not found");
        }

        if ($rowValidators == null) {
            throw new PoInvalidArgumentException("HeaderValidatorCollection service not found");
        }

        if ($sharedService == null) {
            throw new PoInvalidArgumentException("SharedService service not found");
        }

        if ($postingService == null) {
            throw new PoInvalidArgumentException("postingService service not found");
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
            throw new PoRowCreateException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var PORowSnapshot $localSnapshot
         */
        $localSnapshot = $postingService->getCmdRepository()->storeRow($this, $row);

        if ($localSnapshot == null) {
            throw new PoRowException(sprintf("Error occured when creating PO Row #%s", $this->getId()));
        }

        $trigger = null;
        if ($options instanceof PoRowCreateOptions) {
            $trigger = $options->getTriggeredBy();
        }

        $params = [
            "rowId" => $localSnapshot->getId(),
            "rowToken" => $localSnapshot->getToken()
        ];

        $this->addEvent(new PoRowAdded($this->getId(), $trigger, $params));

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
     * @throws PoInvalidArgumentException
     * @throws PoRowCreateException
     * @throws PoRowException
     * @return \Procure\Domain\PurchaseOrder\PORowSnapshot
     */
    public function updateRowFrom(PORowSnapshot $snapshot, CommandOptions $options, $params, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {
        if ($this->getDocStatus() == PODocStatus::DOC_STATUS_POSTED) {
            throw new PoInvalidOperationException(sprintf("PO is posted! %s", $this->getId()));
        }

        if ($snapshot == null) {
            throw new PoInvalidArgumentException("PORowSnapshot not found");
        }

        if ($headerValidators == null) {
            throw new PoInvalidArgumentException("HeaderValidatorCollection service not found");
        }

        if ($rowValidators == null) {
            throw new PoInvalidArgumentException("HeaderValidatorCollection service not found");
        }

        if ($sharedService == null) {
            throw new PoInvalidArgumentException("SharedService service not found");
        }

        if ($postingService == null) {
            throw new PoInvalidArgumentException("postingService service not found");
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
            throw new PoRowCreateException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();

        /**
         *
         * @var PORowSnapshot $localSnapshot
         */
        $localSnapshot = $postingService->getCmdRepository()->storeRow($this, $row);

        if ($localSnapshot == null) {
            throw new PoRowException(sprintf("Error occured when creating PO Row #%s", $this->getId()));
        }

        $trigger = null;
        if ($options instanceof PoRowCreateOptions) {
            $trigger = $options->getTriggeredBy();
        }

        $this->addEvent(new PoRowUpdated($this->getId(), $trigger, $params));

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
            throw new PoInvalidOperationException(sprintf("PO is already posted/closed or being amended! %s", $this->getId()));
        }

        if ($headerValidators == null) {
            throw new PoInvalidArgumentException("HeaderValidatorCollection not found");
        }

        if ($rowValidators == null) {
            throw new PoInvalidArgumentException("HeaderValidatorCollection not found");
        }

        if ($sharedService == null) {
            throw new PoInvalidArgumentException("SharedService service not found");
        }

        if ($postingService == null) {
            throw new PoInvalidArgumentException("postingService service not found");
        }

        if ($options == null) {
            throw new PoInvalidArgumentException("Comnand Options not found!");
        }

        $this->validate($headerValidators, $rowValidators);
        if ($this->hasErrors()) {
            throw new PoPostingException($this->getErrorMessage());
        }

        $this->clearEvents();

        $this->prePost($options, $headerValidators, $rowValidators, $sharedService, $postingService);
        $this->doPost($options, $headerValidators, $rowValidators, $sharedService, $postingService);
        $this->afterPost($options, $headerValidators, $rowValidators, $sharedService, $postingService);

        $this->addEvent(new PoPosted($this->makeSnapshot()));
        return $this;
    }

    /**
     *
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param boolean $isPosting
     * @throws PoInvalidArgumentException
     * @return \Procure\Domain\PurchaseOrder\GenericPO
     */
    public function validate(HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, $isPosting = false)
    {
        if (! $headerValidators instanceof HeaderValidatorCollection) {
            throw new PoInvalidArgumentException("PO Validators not given!");
        }

        if (! $rowValidators instanceof RowValidatorCollection) {
            throw new PoInvalidArgumentException("PO Validators not given!");
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
            throw new PoInvalidArgumentException("PO Validators not given!");
        }

        $headerValidators->validate($this);
    }

    /**
     *
     * @param PORow $row
     * @param RowValidatorCollection $rowValidators
     * @param boolean $isPosting
     * @throws PoInvalidArgumentException
     */
    public function validateRow(PORow $row, RowValidatorCollection $rowValidators, $isPosting = false)
    {
        if (! $row instanceof PORow) {
            throw new PoInvalidArgumentException("Po Row not given!");
        }

        if (! $rowValidators instanceof RowValidatorCollection) {
            throw new PoInvalidArgumentException("Row Validator not given!");
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

    public function makeAPInvoice()
    {
        return APFactory::createAPInvoiceFromPO($this);
    }

    /**
     *
     * @return mixed
     */
    public function getDocRows()
    {
        return $this->docRows;
    }

    /**
     *
     * @param mixed $docRows
     */
    public function setDocRows($docRows)
    {
        $this->docRows = $docRows;
    }

    /**
     *
     * @return mixed
     */
    public function getRowsOutput()
    {
        return $this->rowsOutput;
    }

    /**
     *
     * @param mixed $rowsOutput
     */
    public function setRowsOutput($rowsOutput)
    {
        $this->rowsOutput = $rowsOutput;
    }

    /**
     *
     * @return mixed
     */
    public function getRowIdArray()
    {
        if ($this->rowIdArray == null) {
            return [];
        }
        return $this->rowIdArray;
    }

    /**
     *
     * @param mixed $rowIdArray
     */
    public function setRowIdArray($rowIdArray)
    {
        $this->rowIdArray = $rowIdArray;
    }

    /**
     *
     * @param int $id
     * @return boolean
     */
    public function hasRowId($id)
    {
        if ($this->getRowIdArray() == null)
            return false;

        return in_array($id, $this->getRowIdArray());
    }

    /**
     *
     * @param int $id
     * @param string $token
     * @return NULL|\Procure\Domain\PurchaseOrder\PoRow
     */
    public function getRowbyTokenId($id, $token)
    {
        if ($id == null || $token == null || count($this->getDocRows()) == 0) {
            return null;
        }

        $rows = $this->getDocRows();

        foreach ($rows as $r) {
            if ($r->getId() == $id && $r->getToken() == $token) {
                return $r;
            }
        }

        return null;
    }

    /**
     *
     * @param int $id
     * @param string $token
     * @return NULL|NULL|object
     */
    public function getRowDTObyTokenId($id, $token)
    {
        if ($id == null || $token == null || count($this->getDocRows()) == 0) {
            return null;
        }

        $rows = $this->getDocRows();

        foreach ($rows as $r) {

            /**
             *
             * @var \Procure\Domain\PurchaseOrder\PoRow $r ;
             */
            if ($r->getId() == $id && $r->getToken() == $token) {
                return $r->makeDTOForGrid();
            }
        }

        return null;
    }

    /**
     *
     * @param int $id
     * @param string $token
     * @return NULL|\Procure\Domain\PurchaseOrder\PoRow
     */
    public function getRowbyId($id)
    {
        if ($id == null || $id == null || $this->getDocRows() == null) {
            return null;
        }
        $rows = $this->getDocRows();

        foreach ($rows as $r) {
            if ($r->getId() == $id) {
                return $r;
            }
        }

        return null;
    }
}
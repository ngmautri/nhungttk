<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Procure\Application\DTO\Po\PoDetailsDTO;
use Procure\Domain\APInvoice\Factory\APFactory;
use Procure\Domain\Event\Po\PoHeaderCreated;
use Procure\Domain\Event\Po\PoHeaderUpdated;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\PoUpdateException;
use Procure\Domain\PurchaseOrder\Validator\HeaderValidatorCollection;
use Procure\Domain\PurchaseOrder\Validator\RowValidatorCollection;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\POSpecService;
use Procure\Domain\Event\Po\PoRowAdded;
use Ramsey\Uuid\Uuid;
use Procure\Domain\Event\Po\PoRowUpdated;
use Application\Domain\Shared\Constants;
use Procure\Domain\Exception\PoRowException;
use Procure\Domain\Exception\PoInvalidArgumentException;
use Procure\Domain\Service\SharedService;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\Exception\PoRowCreateException;
use Procure\Application\Command\PO\Options\PoRowCreateOptions;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericPO extends AbstractPO
{

    protected $docRows;

    protected $rowIdArray;

    protected $rowsOutput;

    abstract protected function prePost(POSpecService $specService, POPostingService $postingService);

    abstract protected function doPost(POSpecService $specService, POPostingService $postingService);

    abstract protected function afterPost(POSpecService $specService, POPostingService $postingService);

    abstract protected function raiseEvent();

    abstract protected function preReserve(POSpecService $specService, POPostingService $postingService);

    abstract protected function doReverse(POSpecService $specService, POPostingService $postingService);

    abstract protected function afterReserve(POSpecService $specService, POPostingService $postingService);

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
     * @param POSpecService $specService
     * @param POPostingService $postingService
     * @throws InvalidArgumentException
     * @return \Procure\Domain\PurchaseOrder\GenericPO
     */
    public function post(POSpecService $specService, POPostingService $postingService)
    {
        if ($specService == null) {
            throw new InvalidArgumentException("Specification service not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("Posting service not found");
        }

        $this->validate($specService, TRUE);

        if ($this->hasErrors()) {
            throw new InvalidArgumentException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();

        // template method
        $this->prePost($specService, $postingService);

        // template method
        $this->doPost($specService, $postingService);

        // template method
        $this->afterPost($specService, $postingService);

        if (! $this->hasErrors()) {
            $this->raiseEvent();
        }

        return $this;
    }

    /**
     *
     * @param POSpecService $specService
     * @param boolean $isPosting
     * @throws InvalidArgumentException
     * @return \Procure\Domain\PurchaseOrder\GenericPO
     */
    public function validate(POSpecService $specService, $isPosting = false)
    {
        if ($specService == null) {
            throw new InvalidArgumentException("Specification service not found");
        }

        // Clear Notification.
        $this->clearNotification();

        $this->validateHeader($specService, $isPosting);

        if ($this->hasErrors()) {
            return $this;
        }

        // template method.
        $this->specificValidation($specService, $isPosting);

        if ($this->hasErrors()) {
            return $this;
        }

        if (count($this->docRows) == 0) {
            $this->addError("Doc has no lines");
            return $this;
        }

        foreach ($this->docRows as $row) {
            $this->validateRow($row, $specService, $isPosting);
        }

        return $this;
    }

    /**
     *
     * @param PORowSnapshot $snapshot
     * @param POSpecService $specService
     * @return void|\Procure\Domain\PurchaseOrder\GenericPO
     */
    public function addRowFromSnapshot(PORowSnapshot $snapshot, POSpecService $specService)
    {
        if (! $snapshot instanceof PORowSnapshot) {
            return;
        }

        $this->validateHeader($specService);

        if ($this->hasErrors()) {
            return;
        }

        $snapshot->quantity = $snapshot->docQuantity;
        $snapshot->revisionNo = 0;

        if ($snapshot->token == null) {
            $snapshot->token = Uuid::uuid4();
        }
        $snapshot->docType = $this->docType;
        $snapshot->isDraft = 1;

        $row = PORow::makeFromSnapshot($snapshot);

        $this->validateRow($row, $specService, false);

        $convertedPurchaseQuantity = $row->getDocQuantity();
        $convertedPurchaseUnitPrice = $row->getDocUnitPrice();

        $conversionFactor = $row->getConversionFactor();
        $standardCF = $row->getConversionFactor();

        // converted to purchase quantity
        // $convertedPurchaseQuantity = $convertedPurchaseQuantity * $conversionFactor;
        // $convertedPurchaseUnitPrice = $convertedPurchaseUnitPrice / $conversionFactor;

        // $convertedStandardQuantity = $entity->getDocQuantity();
        // $convertedStandardUnitPrice = $entity->getDocUnitPrice();

        if ($row->hasErrors()) {
            return;
        }

        $this->docRows[] = $row;
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
            throw new PoInvalidArgumentException("Row Validator collection not given!");
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
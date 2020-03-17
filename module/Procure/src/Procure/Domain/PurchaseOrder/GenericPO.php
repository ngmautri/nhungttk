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
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\POSpecService;
use Procure\Domain\Event\Po\PoRowAdded;
use Ramsey\Uuid\Uuid;
use Procure\Domain\Event\Po\PoRowUpdated;

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

    abstract protected function specificHeaderValidation(POSpecService $specService, $isPosting = false);

    abstract protected function specificRowValidation(PORow $row, POSpecService $specService, $isPosting = false);

    abstract protected function specificValidation(POSpecService $specService, $isPosting = false);

    abstract protected function preReserve(POSpecService $specService, POPostingService $postingService);

    abstract protected function doReverse(POSpecService $specService, POPostingService $postingService);

    abstract protected function afterReserve(POSpecService $specService, POPostingService $postingService);

    /**
     *
     * @param POSpecService $specService
     * @param POPostingService $postingService
     * @throws InvalidArgumentException
     * @return int
     */
    public function storeHeader($trigger, $params, POSpecService $specService, POPostingService $postingService)
    {
        if ($specService == null) {
            throw new InvalidArgumentException("Specification service not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("Posting service not found");
        }

        $this->validateHeader($specService);

        if ($this->hasErrors()) {
            throw new InvalidArgumentException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();
        $rootEntityId = $postingService->getCmdRepository()->storeHeader($this, false);

        if (! $rootEntityId == null) {
            $this->addEvent(new PoHeaderCreated($rootEntityId, $trigger, $params));
        }

        return $rootEntityId;
    }

    /**
     *
     * @param POSpecService $specService
     * @param POPostingService $postingService
     * @throws InvalidArgumentException
     * @return int
     */
    public function updateHeader($trigger, $params, POSpecService $specService, POPostingService $postingService)
    {
        if ($specService == null) {
            throw new InvalidArgumentException("Specification service not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("Posting service not found");
        }

        $this->validateHeader($specService);

        if ($this->hasErrors()) {
            throw new InvalidArgumentException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();
        $rootEntityId = $postingService->getCmdRepository()->storeHeader($this, false);

        if (! $rootEntityId == null) {
            $this->addEvent(new PoHeaderUpdated($rootEntityId, $trigger, $params));
        }

        return $rootEntityId;
    }

    /**
     *
     * @param POSpecService $specService
     * @param POPostingService $postingService
     * @throws InvalidArgumentException
     * @return int
     */
    public function storeRow($trigger, $params, PORowSnapshot $snapshot, POSpecService $specService, POPostingService $postingService)
    {
        if ($snapshot == null) {
            throw new InvalidArgumentException("PORowSnapshot not found");
        }

        if ($specService == null) {
            throw new InvalidArgumentException("Specification service not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("Posting service not found");
        }

        $snapshot->quantity = $snapshot->docQuantity;
        $snapshot->revisionNo = 0;

        if ($snapshot->token == null) {
            $snapshot->token = Uuid::uuid4();
        }

        $snapshot->docType = $this->docType;
        $snapshot->isDraft = 1;
        $snapshot->unitPrice = $snapshot->getDocUnitPrice();
        $snapshot->unit = $snapshot->getDocUnit();

        $row = PORow::makeFromSnapshot($snapshot);

        $this->validateRow($row, $specService, false);

        if ($this->hasErrors()) {
            throw new InvalidArgumentException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();
        $localEntityId = $postingService->getCmdRepository()->storeRow($this, $row);
        
        if (! $localEntityId == null) {

            $params = [
                "rowId" => $localEntityId,
                "rowToken" => "",                
            ];

            $this->addEvent(new PoRowAdded($this->getId(), $trigger, $params));
        }

        return $localEntityId;
    }

    /**
     *
     * @param POSpecService $specService
     * @param POPostingService $postingService
     * @throws InvalidArgumentException
     * @return int
     */
    public function updateRow($trigger, $params, POSpecService $specService, POPostingService $postingService)
    {
        if ($specService == null) {
            throw new InvalidArgumentException("Specification service not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("Posting service not found");
        }

        $this->validateHeader($specService);

        if ($this->hasErrors()) {
            throw new InvalidArgumentException($this->getNotification()->errorMessage());
        }

        $this->recordedEvents = array();
        $rootEntityId = $postingService->getCmdRepository()->storeRow($this, false);

        if (! $rootEntityId == null) {
            $this->addEvent(new PoHeaderUpdated($rootEntityId, $trigger, $params));
        }

        return $rootEntityId;
    }

    /**
     *
     * @param string $trigger
     * @param array $params
     * @param PORow $row
     * @param PoRowSnapshot $snapshot
     * @param POSpecService $specService
     * @param POPostingService $postingService
     * @throws PoUpdateException
     * @return void|\Procure\Domain\PurchaseOrder\GenericPO
     */
    public function updateRowFromSnapshot($trigger, $params, PORow $row, PoRowSnapshot $snapshot, POSpecService $specService, POPostingService $postingService)
    {
        if (! $snapshot instanceof PoRowSnapshot || ! $row instanceof PORow || $specService == null || $postingService == null) {
            return;
        }

        // quantity can not be null;
        $snapshot->quantity = $snapshot->getDocQuantity();
        $snapshot->unitPrice = $snapshot->getDocUnitPrice();
        $snapshot->unit = $snapshot->getDocUnit();

        SnapshotAssembler::makeFromSnapshot($row, $snapshot);

        $this->validateRow($row, $specService);

        if ($this->hasErrors()) {
            throw new PoUpdateException($this->getErrorMessage());
        }

        $this->recordedEvents = array();
        $postingService->getCmdRepository()->storeRow($this, $row, false);

        if ($this->hasErrors()) {
            throw new PoUpdateException($this->getErrorMessage());
        }

        $this->addEvent(new PoRowUpdated($this->getId(), $trigger, $params));

        return $this;
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
     * @param POSpecService $specService
     * @param boolean $isPosting
     */
    public function validateHeader(POSpecService $specService, $isPosting = false)
    {
        try {

            if ($specService == null) {
                $this->addError("Specification service not found");
            }

            // general validation done
            $this->generalHeaderValidation($specService, $isPosting);

            if ($this->hasErrors()) {
                return;
            }

            // Check and set exchange rate
            // if ($isPosting) {
            $fxRate = $specService->getFxService()->checkAndReturnFX($this->getDocCurrency(), $this->getLocalCurrency(), $this->getExchangeRate());
            $this->exchangeRate = $fxRate;
            // }

            // specific validation - template method
            $this->specificHeaderValidation($specService, $isPosting);
        } catch (\Exception $e) {
            $this->addError($e->getMessage());
        }
    }

    /**
     *
     * @param POSpecService $specService
     * @param boolean $isPosting
     * @return GenericPO
     */
    protected function generalHeaderValidation(POSpecService $specService, $isPosting = false)
    {
        if ($specService == null) {
            $this->addError("Specification not found");
            return;
        }

        /**
         *
         * @var POSpecService $specService ;
         */
        $specService->doGeneralHeaderValiation($this);
    }

    /**
     *
     * @param PORow $row
     * @param POSpecService $specService
     * @param boolean $isPosting
     */
    public function validateRow(PORow $row, POSpecService $specService, $isPosting = false)
    {
        try {

            if ($specService == null) {
                $this->addError("Specification service not found");
                return;
            }

            // general validation done
            $this->generalRowValidation($row, $specService, $isPosting);

            // specific validation
            $this->specificRowValidation($row, $specService, $isPosting);

            if ($row->hasErrors()) {
                $this->addErrorArray($row->getErrors());
            }
        } catch (\Exception $e) {
            $this->addError($e->getMessage());
        }
    }

    /**
     *
     * @param PORow $row
     * @param POSpecService $specService
     * @param boolean $isPosting
     * @return \Procure\Domain\PurchaseOrder\GenericPO
     */
    protected function generalRowValidation(PORow $row, POSpecService $specService, $isPosting = false)
    {
        if ($specService == null) {
            $this->addError("Specification not found");
            return;
        }
        $specService->doGeneralRowValiation($this, $row, $isPosting);
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
        if($id==null|| $id==null || $this->getDocRows()==null){
            return null;
        }
        $rows = $this->getDocRows();
        
        foreach ($rows as $r){
            if ($r->getId() == $id) {
                return $r;
            }
        }
        
        return null;
   }

}
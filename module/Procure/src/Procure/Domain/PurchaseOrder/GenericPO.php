<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Notification;
use Procure\Application\DTO\Po\PoDetailsDTO;
use Procure\Domain\APInvoice\Factory\APFactory;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\POSpecService;
use Application\Domain\Shared\DTOFactory;
use Procure\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericPO extends AbstractPO
{

    protected $docRows;

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
     * @param PORowSnapshot $snapshot
     * @param POSpecService $specService
     * @param Notification $notification
     * @throws InvalidArgumentException
     * @return NULL|\Procure\Domain\PurchaseOrder\GenericPO
     */
    public function addRowFromSnapshot(PORowSnapshot $snapshot, POSpecService $specService)
    {
        if (! $snapshot instanceof PORowSnapshot) {
            return null;
        }

        // $snapshot-> = $this->uuid;
        $snapshot->docType = $this->docType;
        $snapshot->quantity = $snapshot->docQuantity;

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

        if ($ckResult->hasErrors()) {
            throw new InvalidArgumentException($ckResult->errorMessage());
        }

        $this->docRows[] = $row;
        return $this;
    }

    /**
     *
     * @param POSpecService $specService
     * @param Notification $notification
     * @param boolean $isPosting
     * @return \Application\Notification
     */
    public function validateHeader(POSpecService $specService, Notification $notification = null, $isPosting = false)
    {
        try {

            if ($specService == null) {
                $this->addError("Specification service not found");
            }

            // general validation done
            $this->generalHeaderValidation($specService, $isPosting);

            if ($this->hasErrors()) {
                return $this;
            }

            // Check and set exchange rate
            // if ($isPosting) {
            $fxRate = $specService->getFxService()->checkAndReturnFX($this->getDocCurrency(), $this->getLocalCurrency(), $this->getExchangeRate());
            $this->exchangeRate = $fxRate;
            // }

            // specific validation - template method
            $this->specificHeaderValidation($specService, $isPosting);
        } catch (\Exception $e) {
            $notification->addError($e->getMessage());
        }

        return $notification;
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
            return $this;
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
     * @param Notification $notification
     * @param boolean $isPosting
     * @return \Application\Notification
     */
    public function validateRow(PORow $row, POSpecService $specService, $isPosting = false)
    {
        try {

            if ($specService == null) {
                $this->addError("Specification service not found");
                return $this;
            }

            // general validation done
            $this->generalRowValidation($row, $specService, $isPosting);

            if ($this->hasErrors()) {
                return $this;
            }

            // specific validation
            $this->specificRowValidation($row, $specService, $isPosting);
            
        } catch (\Exception $e) {
            $this->addError($e->getMessage());
        }

        return $this;
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
            return $this;
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

        if (count($this->docRows) > 0) {
            foreach ($this->docRows as $row) {

                if ($row instanceof PORow) {
                    $dto->docRowsDTO[] = $row->makeDTOForGrid();
                }
            }
        }
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
}
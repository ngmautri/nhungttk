<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Notification;
use Procure\Application\DTO\Po\PoDetailsDTO;
use Procure\Domain\APInvoice\Factory\APFactory;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\POSpecificationService;
use Application\Domain\Shared\DTOFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericPO extends AbstractPO
{

    protected $docRows;

    protected $rowsOutput;
    
    abstract protected function prePost(POSpecificationService $specificationService,POPostingService $postingService, Notification $notification = null);
    
    abstract protected function doPost(POSpecificationService $specificationService, POPostingService $postingService, Notification $notification = null);
    
    abstract protected function afterPost(POSpecificationService $specificationService, POPostingService $postingService, Notification $notification = null);
    
    abstract protected function raiseEvent();
    
    abstract protected function specificHeaderValidation(POSpecificationService $specificationService, Notification $notification, $isPosting = false);
    
    abstract protected function specificRowValidation(PORow $row, POPostingService $specificationService, Notification $notification, $isPosting = false);
    
    abstract protected function specificValidation(POSpecificationService $specificationService, Notification $notification, $isPosting = false);
    
    abstract protected function preReserve(POSpecificationService $specificationService, POPostingService $postingService, Notification $notification = null);
    
    abstract protected function doReverse(POSpecificationService $specificationService, POPostingService $postingService, Notification $notification = null);
    
    abstract protected function afterReserve(POSpecificationService $specificationService, POPostingService $postingService, Notification $notification = null);
    
    
    /**
     * 
     * @param POSpecificationService $specificationService
     * @param Notification $notification
     * @param boolean $isPosting
     * @return \Application\Notification
     */
    public function validateHeader(POSpecificationService $specificationService, Notification $notification = null, $isPosting = false)
    {
        try {
            
            if ($notification == null) {
                $notification = new Notification();
            }
            
            if ($specificationService == null) {
                $notification->addError("Specification service not found");
                return $notification;
            }
            
            // general validation done
            $notification = $this->generalHeaderValidation($specificationService, $notification, $isPosting);
            
            if ($notification->hasErrors()) {
                return $notification;
            }
            
            // Check and set exchange rate
            // if ($isPosting) {
            $fxRate = $specificationService->getFxService()->checkAndReturnFX($this->getDocCurrency(), $this->getLocalCurrency(), $this->getExchangeRate());
            $this->exchangeRate = $fxRate;
            // }
            
            // specific validation
            $specificHeaderValidationResult = $this->specificHeaderValidation($specificationService, $notification, $isPosting);
            
            if ($specificHeaderValidationResult instanceof Notification) {
                $notification = $specificHeaderValidationResult;
            }
        } catch (\Exception $e) {
            $notification->addError($e->getMessage());
        }
        
        return $notification;
    }
    
    /**
     * 
     * @param POSpecificationService $specificationService
     * @param Notification $notification
     * @param boolean $isPosting
     * @return \Application\Notification
     */
    protected function generalHeaderValidation(POSpecificationService $specificationService, Notification $notification, $isPosting = false)
    {
        if ($notification == null)
            $notification = new Notification();
            
            if ($specificationService == null) {
                $notification->addError("Specification not found");
                return $notification;
            }
            
            /**
             *
             * @var POSpecificationService $specificationService ;
             */
            
            $notification = $specificationService->doGeneralHeaderValiation($this, $notification);
            
            return $notification;
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
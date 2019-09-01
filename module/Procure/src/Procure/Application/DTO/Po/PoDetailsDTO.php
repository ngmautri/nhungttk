<?php
namespace Procure\Application\DTO\Po;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoDetailsDTO extends PoDTO
{

    public $paymentTermName;
    
    public $paymentTermCode;
    
    public $warehouseName;
    
    public $warehouseCode;
    
    public $paymentMethodName;
    
    public $paymentMethodCode;
    
    public $incotermCode;
    
    public $incotermName;
    
    public $createdByName;
    
    public $lastChangedByName;
    
    public $totalRows;
    
    public $totalActiveRows;
    
    public $maxRowNumber;
    
    public $netAmount;
    
    public $taxAmount;
    
    public $grossAmount;
    
    public $discountAmount;
    
    public $billedAmount;
    
    public $completedRows;
    
    public $docRowsDTO;
    /**
     * @return mixed
     */
    public function getPaymentTermName()
    {
        return $this->paymentTermName;
    }

    /**
     * @return mixed
     */
    public function getPaymentTermCode()
    {
        return $this->paymentTermCode;
    }

    /**
     * @return mixed
     */
    public function getWarehouseName()
    {
        return $this->warehouseName;
    }

    /**
     * @return mixed
     */
    public function getWarehouseCode()
    {
        return $this->warehouseCode;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethodName()
    {
        return $this->paymentMethodName;
    }

    /**
     * @return mixed
     */
    public function getPaymentMethodCode()
    {
        return $this->paymentMethodCode;
    }

    /**
     * @return mixed
     */
    public function getIncotermCode()
    {
        return $this->incotermCode;
    }

    /**
     * @return mixed
     */
    public function getIncotermName()
    {
        return $this->incotermName;
    }

    /**
     * @return mixed
     */
    public function getCreatedByName()
    {
        return $this->createdByName;
    }

    /**
     * @return mixed
     */
    public function getLastChangedByName()
    {
        return $this->lastChangedByName;
    }

    /**
     * @return mixed
     */
    public function getTotalRows()
    {
        return $this->totalRows;
    }

    /**
     * @return mixed
     */
    public function getTotalActiveRows()
    {
        return $this->totalActiveRows;
    }

    /**
     * @return mixed
     */
    public function getMaxRowNumber()
    {
        return $this->maxRowNumber;
    }

    /**
     * @return mixed
     */
    public function getNetAmount()
    {
        return $this->netAmount;
    }

    /**
     * @return mixed
     */
    public function getTaxAmount()
    {
        return $this->taxAmount;
    }

    /**
     * @return mixed
     */
    public function getGrossAmount()
    {
        return $this->grossAmount;
    }

    /**
     * @return mixed
     */
    public function getDiscountAmount()
    {
        return $this->discountAmount;
    }

    /**
     * @return mixed
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     * @return mixed
     */
    public function getCompletedRows()
    {
        return $this->completedRows;
    }

    /**
     * @return mixed
     */
    public function getDocRowsDTO()
    {
        return $this->docRowsDTO;
    }

}

<?php
namespace Inventory\Infrastructure\Persistence\SQL\Filter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemSerialSqlFilter extends InventoryQuerySqlFilter
{

    public $docYear;

    public $docMonth;

    public $itemId;

    public $vendorId;

    public $invoiceId;

    public $grId;

    public $isActive;

    public function __toString()
    {
        $f = "ItemSerialSqlFilter_%s_%s_%s_%s_%s_%s_%s";
        return \sprintf($f, $this->getDocMonth(), $this->getDocYear(), $this->getItemId(), $this->getVendorId(), $this->getInvoiceId(), $this->getGrId(), $this->getIsActive());
    }

    /*
     * |=============================
     * |Setter and Gettter
     * |
     * |=============================
     */
    /**
     *
     * @return mixed
     */
    public function getDocYear()
    {
        return $this->docYear;
    }

    /**
     *
     * @return mixed
     */
    public function getDocMonth()
    {
        return $this->docMonth;
    }

    /**
     *
     * @return mixed
     */
    public function getItemId()
    {
        return $this->itemId;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorId()
    {
        return $this->vendorId;
    }

    /**
     *
     * @return mixed
     */
    public function getInvoiceId()
    {
        return $this->invoiceId;
    }

    /**
     *
     * @return mixed
     */
    public function getGrId()
    {
        return $this->grId;
    }

    /**
     *
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     *
     * @param mixed $docYear
     */
    public function setDocYear($docYear)
    {
        $this->docYear = $docYear;
    }

    /**
     *
     * @param mixed $docMonth
     */
    public function setDocMonth($docMonth)
    {
        $this->docMonth = $docMonth;
    }

    /**
     *
     * @param mixed $itemId
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;
    }

    /**
     *
     * @param mixed $vendorId
     */
    public function setVendorId($vendorId)
    {
        $this->vendorId = $vendorId;
    }

    /**
     *
     * @param mixed $invoiceId
     */
    public function setInvoiceId($invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    /**
     *
     * @param mixed $grId
     */
    public function setGrId($grId)
    {
        $this->grId = $grId;
    }

    /**
     *
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }
}

<?php
namespace Inventory\Domain\Item\Serial;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class BaseSerial extends AbstractSerial

{

    protected $vendorName;

    protected $invoiceSysNumber;

    protected $invoiceId;

    protected $invoiceToken;

    protected $grSysNumber;

    protected $grId;

    protected $grToken;

    protected $itemName;

    protected $itemToken;

    /**
     *
     * @param BaseSerial $other
     * @return boolean|unknown
     */
    public function equals(BaseSerial $other)
    {
        if ($other == null) {
            return false;
        }

        return $this->getId()->equals($other->getId());
    }

    /**
     *
     * @return object
     */
    public function makeSnapshot()
    {
        return GenericObjectAssembler::updateAllFieldsFrom(new SerialSnapshot(), $this);
    }

    /*
     * |=============================
     * | Getter and Setter
     * |
     * |=============================
     */

    /**
     *
     * @return mixed
     */
    public function getInvoiceSysNumber()
    {
        return $this->invoiceSysNumber;
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
    public function getInvoiceToken()
    {
        return $this->invoiceToken;
    }

    /**
     *
     * @return mixed
     */
    public function getGrSysNumber()
    {
        return $this->grSysNumber;
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
    public function getGrToken()
    {
        return $this->grToken;
    }

    /**
     *
     * @param mixed $invoiceSysNumber
     */
    protected function setInvoiceSysNumber($invoiceSysNumber)
    {
        $this->invoiceSysNumber = $invoiceSysNumber;
    }

    /**
     *
     * @param mixed $invoiceId
     */
    protected function setInvoiceId($invoiceId)
    {
        $this->invoiceId = $invoiceId;
    }

    /**
     *
     * @param mixed $invoiceToken
     */
    protected function setInvoiceToken($invoiceToken)
    {
        $this->invoiceToken = $invoiceToken;
    }

    /**
     *
     * @param mixed $grSysNumber
     */
    protected function setGrSysNumber($grSysNumber)
    {
        $this->grSysNumber = $grSysNumber;
    }

    /**
     *
     * @param mixed $grId
     */
    protected function setGrId($grId)
    {
        $this->grId = $grId;
    }

    /**
     *
     * @param mixed $grToken
     */
    protected function setGrToken($grToken)
    {
        $this->grToken = $grToken;
    }

    /**
     *
     * @param mixed $itemName
     */
    protected function setItemName($itemName)
    {
        $this->itemName = $itemName;
    }

    /**
     *
     * @param mixed $itemToken
     */
    protected function setItemToken($itemToken)
    {
        $this->itemToken = $itemToken;
    }

    /**
     *
     * @return mixed
     */
    public function getItemName()
    {
        return $this->itemName;
    }

    /**
     *
     * @return mixed
     */
    public function getItemToken()
    {
        return $this->itemToken;
    }

    /**
     *
     * @return mixed
     */
    protected function getVendorName()
    {
        return $this->vendorName;
    }

    /**
     *
     * @param mixed $vendorName
     */
    protected function setVendorName($vendorName)
    {
        $this->vendorName = $vendorName;
    }
}

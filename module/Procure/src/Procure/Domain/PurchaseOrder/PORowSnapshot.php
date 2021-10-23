<?php
namespace Procure\Domain\PurchaseOrder;

use Procure\Domain\RowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PORowSnapshot extends RowSnapshot
{

    /*
     * |=============================
     * | Procure\Domain\PurchaseOrder\BaseRow
     * |
     * |=============================
     */
    public $confirmedGrBalance;

    public $openAPAmount;

    public $billedAmount;

    public $openGrBalance;

    public $draftAPQuantity;

    public $postedAPQuantity;

    public $openAPQuantity;

    /**
     *
     * @return mixed
     */
    public function getConfirmedGrBalance()
    {
        return $this->confirmedGrBalance;
    }

    /**
     *
     * @param mixed $confirmedGrBalance
     */
    public function setConfirmedGrBalance($confirmedGrBalance)
    {
        $this->confirmedGrBalance = $confirmedGrBalance;
    }

    /**
     *
     * @return mixed
     */
    public function getOpenAPAmount()
    {
        return $this->openAPAmount;
    }

    /**
     *
     * @param mixed $openAPAmount
     */
    public function setOpenAPAmount($openAPAmount)
    {
        $this->openAPAmount = $openAPAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     *
     * @param mixed $billedAmount
     */
    public function setBilledAmount($billedAmount)
    {
        $this->billedAmount = $billedAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getOpenGrBalance()
    {
        return $this->openGrBalance;
    }

    /**
     *
     * @param mixed $openGrBalance
     */
    public function setOpenGrBalance($openGrBalance)
    {
        $this->openGrBalance = $openGrBalance;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftAPQuantity()
    {
        return $this->draftAPQuantity;
    }

    /**
     *
     * @param mixed $draftAPQuantity
     */
    public function setDraftAPQuantity($draftAPQuantity)
    {
        $this->draftAPQuantity = $draftAPQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedAPQuantity()
    {
        return $this->postedAPQuantity;
    }

    /**
     *
     * @param mixed $postedAPQuantity
     */
    public function setPostedAPQuantity($postedAPQuantity)
    {
        $this->postedAPQuantity = $postedAPQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getOpenAPQuantity()
    {
        return $this->openAPQuantity;
    }

    /**
     *
     * @param mixed $openAPQuantity
     */
    public function setOpenAPQuantity($openAPQuantity)
    {
        $this->openAPQuantity = $openAPQuantity;
    }
}
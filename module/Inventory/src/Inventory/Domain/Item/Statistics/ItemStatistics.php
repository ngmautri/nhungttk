<?php
namespace Inventory\Domain\Item\Statistics;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemStatistics
{

    private $totalPicture = 0;

    private $totalVariant = 0;

    private $totalSerial = 0;

    private $totalAttachment = 0;

    private $totalPR = 0;

    private $totalQO = 0;

    private $totalPO = 0;

    private $totalAP = 0;

    private $totalAssosiation = 0;

    /**
     *
     * @return number
     */
    public function getTotalPicture()
    {
        return $this->totalPicture;
    }

    /**
     *
     * @param number $totalPicture
     */
    public function setTotalPicture($totalPicture)
    {
        $this->totalPicture = $totalPicture;
    }

    /**
     *
     * @return number
     */
    public function getTotalVariant()
    {
        return $this->totalVariant;
    }

    /**
     *
     * @param number $totalVariant
     */
    public function setTotalVariant($totalVariant)
    {
        $this->totalVariant = $totalVariant;
    }

    /**
     *
     * @return number
     */
    public function getTotalSerial()
    {
        return $this->totalSerial;
    }

    /**
     *
     * @param number $totalSerial
     */
    public function setTotalSerial($totalSerial)
    {
        $this->totalSerial = $totalSerial;
    }

    /**
     *
     * @return number
     */
    public function getTotalAttachment()
    {
        return $this->totalAttachment;
    }

    /**
     *
     * @param number $totalAttachment
     */
    public function setTotalAttachment($totalAttachment)
    {
        $this->totalAttachment = $totalAttachment;
    }

    /**
     *
     * @return number
     */
    public function getTotalPR()
    {
        return $this->totalPR;
    }

    /**
     *
     * @param number $totalPR
     */
    public function setTotalPR($totalPR)
    {
        $this->totalPR = $totalPR;
    }

    /**
     *
     * @return number
     */
    public function getTotalQO()
    {
        return $this->totalQO;
    }

    /**
     *
     * @param number $totalQO
     */
    public function setTotalQO($totalQO)
    {
        $this->totalQO = $totalQO;
    }

    /**
     *
     * @return number
     */
    public function getTotalPO()
    {
        return $this->totalPO;
    }

    /**
     *
     * @param number $totalPO
     */
    public function setTotalPO($totalPO)
    {
        $this->totalPO = $totalPO;
    }

    /**
     *
     * @return number
     */
    public function getTotalAP()
    {
        return $this->totalAP;
    }

    /**
     *
     * @param number $totalAP
     */
    public function setTotalAP($totalAP)
    {
        $this->totalAP = $totalAP;
    }

    /**
     *
     * @return number
     */
    public function getTotalAssosiation()
    {
        return $this->totalAssosiation;
    }

    /**
     *
     * @param number $totalAssosiation
     */
    public function setTotalAssosiation($totalAssosiation)
    {
        $this->totalAssosiation = $totalAssosiation;
    }
}

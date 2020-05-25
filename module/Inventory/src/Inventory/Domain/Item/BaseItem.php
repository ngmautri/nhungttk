<?php
namespace Inventory\Domain\Item;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BaseItem extends AbstractItem
{

    // addtional attribute.
    // =======================================
    protected $pictureList;

    protected $attachmentList;

    protected $prList;

    protected $poList;

    protected $apList;

    protected $serialNoList;

    protected $batchList;

    protected $fifoLayerList;

    protected $onHandQty;

    protected $onHandValue;

    // =======================================

    // Addtional
    protected $standardUnitName;

    /**
     *
     * @return mixed
     */
    public function getOnHandValue()
    {
        return $this->onHandValue;
    }

    /**
     *
     * @param mixed $onHandValue
     */
    public function setOnHandValue($onHandValue)
    {
        $this->onHandValue = $onHandValue;
    }

    // =======================================

    /**
     *
     * @return mixed
     */
    public function getPictureList()
    {
        return $this->pictureList;
    }

    /**
     *
     * @return mixed
     */
    public function getAttachmentList()
    {
        return $this->attachmentList;
    }

    /**
     *
     * @return mixed
     */
    public function getPrList()
    {
        return $this->prList;
    }

    /**
     *
     * @return mixed
     */
    public function getPoList()
    {
        return $this->poList;
    }

    /**
     *
     * @return mixed
     */
    public function getApList()
    {
        return $this->apList;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialNoList()
    {
        return $this->serialNoList;
    }

    /**
     *
     * @return mixed
     */
    public function getBatchList()
    {
        return $this->batchList;
    }

    /**
     *
     * @return mixed
     */
    public function getFifoLayerList()
    {
        return $this->fifoLayerList;
    }

    /**
     *
     * @return mixed
     */
    public function getOnHandQty()
    {
        return $this->onHandQty;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardUnitName()
    {
        return $this->standardUnitName;
    }

    /**
     *
     * @param mixed $pictureList
     */
    public function setPictureList($pictureList)
    {
        $this->pictureList = $pictureList;
    }

    /**
     *
     * @param mixed $attachmentList
     */
    public function setAttachmentList($attachmentList)
    {
        $this->attachmentList = $attachmentList;
    }

    /**
     *
     * @param mixed $prList
     */
    public function setPrList($prList)
    {
        $this->prList = $prList;
    }

    /**
     *
     * @param mixed $poList
     */
    public function setPoList($poList)
    {
        $this->poList = $poList;
    }

    /**
     *
     * @param mixed $apList
     */
    public function setApList($apList)
    {
        $this->apList = $apList;
    }

    /**
     *
     * @param mixed $serialNoList
     */
    public function setSerialNoList($serialNoList)
    {
        $this->serialNoList = $serialNoList;
    }

    /**
     *
     * @param mixed $batchList
     */
    public function setBatchList($batchList)
    {
        $this->batchList = $batchList;
    }

    /**
     *
     * @param mixed $fifoLayerList
     */
    public function setFifoLayerList($fifoLayerList)
    {
        $this->fifoLayerList = $fifoLayerList;
    }

    /**
     *
     * @param mixed $onHandQty
     */
    public function setOnHandQty($onHandQty)
    {
        $this->onHandQty = $onHandQty;
    }

    /**
     *
     * @param mixed $standardUnitName
     */
    public function setStandardUnitName($standardUnitName)
    {
        $this->standardUnitName = $standardUnitName;
    }
}
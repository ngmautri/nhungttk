<?php
namespace Inventory\Domain\Item;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GenericItemSnapshot extends BaseItemSnapshot
{

    public $assetLabel1;

    public $serialNo;

    public $serialNo1;

    public $serialNo2;

    public $serialMfgNumber;

    public $serialMfgDate;

    public $serialWarrantyStartDate;

    public $serialWarrantyEndDate;

    public $serialMfgName;

    public $serialMfgName1;

    public $serialMfgModel;

    public $serialMfgModel1;

    public $serialERPNumber;

    public $serialERPNumber1;

    public $serialLotNumber;

    public $serialId;

    public $serialSystemNo;

    public $qoList;

    public $procureGrList;

    public $batchNoList;

    public $fifoLayerConsumeList;

    public $stockGrList;

    public $pictureList;

    public $attachmentList;

    public $prList;

    public $poList;

    public $apList;

    public $serialNoList;

    public $batchList;

    public $fifoLayerList;

    public $onHandQty;

    public $onHandValue;

    public $standardUnitName;

    /**
     *
     * @return mixed
     */
    public function getQoList()
    {
        return $this->qoList;
    }

    /**
     *
     * @return mixed
     */
    public function getProcureGrList()
    {
        return $this->procureGrList;
    }

    /**
     *
     * @return mixed
     */
    public function getBatchNoList()
    {
        return $this->batchNoList;
    }

    /**
     *
     * @return mixed
     */
    public function getFifoLayerConsumeList()
    {
        return $this->fifoLayerConsumeList;
    }

    /**
     *
     * @return mixed
     */
    public function getStockGrList()
    {
        return $this->stockGrList;
    }

    /**
     *
     * @return mixed
     */
    public function getAssetLabel1()
    {
        return $this->assetLabel1;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialNo()
    {
        return $this->serialNo;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialNo1()
    {
        return $this->serialNo1;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialNo2()
    {
        return $this->serialNo2;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialMfgNumber()
    {
        return $this->serialMfgNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialMfgDate()
    {
        return $this->serialMfgDate;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialWarrantyStartDate()
    {
        return $this->serialWarrantyStartDate;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialWarrantyEndDate()
    {
        return $this->serialWarrantyEndDate;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialMfgName()
    {
        return $this->serialMfgName;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialMfgName1()
    {
        return $this->serialMfgName1;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialMfgModel()
    {
        return $this->serialMfgModel;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialMfgModel1()
    {
        return $this->serialMfgModel1;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialERPNumber()
    {
        return $this->serialERPNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialERPNumber1()
    {
        return $this->serialERPNumber1;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialLotNumber()
    {
        return $this->serialLotNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialId()
    {
        return $this->serialId;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialSystemNo()
    {
        return $this->serialSystemNo;
    }

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
    public function getOnHandValue()
    {
        return $this->onHandValue;
    }

    /**
     *
     * @return mixed
     */
    public function getStandardUnitName()
    {
        return $this->standardUnitName;
    }
}
<?php
namespace Inventory\Domain\Item;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BaseItem extends AbstractItem
{

    // addtional serial number.
    // =======================================
    protected $assetLabel1;

    // addtional serial number.
    // =======================================
    protected $serialNo;

    protected $serialNo1;

    protected $serialNo2;

    protected $serialMfgNumber;

    protected $serialMfgDate;

    protected $serialWarrantyStartDate;

    protected $serialWarrantyEndDate;

    protected $serialMfgName;

    protected $serialMfgName1;

    protected $serialMfgModel;

    protected $serialMfgModel1;

    protected $serialERPNumber;

    protected $serialERPNumber1;

    protected $serialLotNumber;

    protected $serialId;

    protected $serialSystemNo;

    // addtional attribute.
    // =======================================
    protected $qoList;

    protected $procureGrList;

    protected $batchNoList;

    protected $fifoLayerConsumeList;

    protected $stockGrList;

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

    protected $standardUnitName;

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
     * @param mixed $assetLabel1
     */
    public function setAssetLabel1($assetLabel1)
    {
        $this->assetLabel1 = $assetLabel1;
    }

    public static function createSnapshotProps()
    {
        $baseClass = "Inventory\Domain\Item\BaseItem";
        $entity = new self();
        $reflectionClass = new \ReflectionClass($entity);

        $props = $reflectionClass->getProperties();

        foreach ($props as $property) {
            // echo $property->class . "\n";
            if ($property->class == $reflectionClass->getName() || $property->class == $baseClass) {
                $property->setAccessible(true);
                $propertyName = $property->getName();
                print "\n" . "public $" . $propertyName . ";";
            }
        }
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
     * @param mixed $serialNo
     */
    public function setSerialNo($serialNo)
    {
        $this->serialNo = $serialNo;
    }

    /**
     *
     * @param mixed $serialNo1
     */
    public function setSerialNo1($serialNo1)
    {
        $this->serialNo1 = $serialNo1;
    }

    /**
     *
     * @param mixed $serialNo2
     */
    public function setSerialNo2($serialNo2)
    {
        $this->serialNo2 = $serialNo2;
    }

    /**
     *
     * @param mixed $serialMfgNumber
     */
    public function setSerialMfgNumber($serialMfgNumber)
    {
        $this->serialMfgNumber = $serialMfgNumber;
    }

    /**
     *
     * @param mixed $serialMfgDate
     */
    public function setSerialMfgDate($serialMfgDate)
    {
        $this->serialMfgDate = $serialMfgDate;
    }

    /**
     *
     * @param mixed $serialWarrantyStartDate
     */
    public function setSerialWarrantyStartDate($serialWarrantyStartDate)
    {
        $this->serialWarrantyStartDate = $serialWarrantyStartDate;
    }

    /**
     *
     * @param mixed $serialWarrantyEndDate
     */
    public function setSerialWarrantyEndDate($serialWarrantyEndDate)
    {
        $this->serialWarrantyEndDate = $serialWarrantyEndDate;
    }

    /**
     *
     * @param mixed $serialMfgName
     */
    public function setSerialMfgName($serialMfgName)
    {
        $this->serialMfgName = $serialMfgName;
    }

    /**
     *
     * @param mixed $serialMfgName1
     */
    public function setSerialMfgName1($serialMfgName1)
    {
        $this->serialMfgName1 = $serialMfgName1;
    }

    /**
     *
     * @param mixed $serialMfgModel
     */
    public function setSerialMfgModel($serialMfgModel)
    {
        $this->serialMfgModel = $serialMfgModel;
    }

    /**
     *
     * @param mixed $serialMfgModel1
     */
    public function setSerialMfgModel1($serialMfgModel1)
    {
        $this->serialMfgModel1 = $serialMfgModel1;
    }

    /**
     *
     * @param mixed $serialERPNumber
     */
    public function setSerialERPNumber($serialERPNumber)
    {
        $this->serialERPNumber = $serialERPNumber;
    }

    /**
     *
     * @param mixed $serialERPNumber1
     */
    public function setSerialERPNumber1($serialERPNumber1)
    {
        $this->serialERPNumber1 = $serialERPNumber1;
    }

    /**
     *
     * @param mixed $serialLotNumber
     */
    public function setSerialLotNumber($serialLotNumber)
    {
        $this->serialLotNumber = $serialLotNumber;
    }

    /**
     *
     * @param mixed $serialId
     */
    public function setSerialId($serialId)
    {
        $this->serialId = $serialId;
    }

    /**
     *
     * @param mixed $serialSystemNo
     */
    public function setSerialSystemNo($serialSystemNo)
    {
        $this->serialSystemNo = $serialSystemNo;
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
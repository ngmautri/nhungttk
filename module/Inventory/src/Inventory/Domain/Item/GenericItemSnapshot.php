<?php
namespace Inventory\Domain\Item;

use Ramsey\Uuid\Uuid;

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

    public $associationList;

    public $backwardAssociationList;

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

    public $statistics;

    public $variantCollection;

    public function initDoc($createdBy, $createdDate)
    {
        $this->setCreatedOn($createdDate);
        $this->setCreatedBy($createdBy);

        $this->setIsActive(1);
        $this->setRevisionNo(0);
        $this->setUuid(Uuid::uuid4()->toString());
        $this->setToken($this->getUuid());
    }

    public function updateDoc($createdBy, $createdDate)
    {
        $this->setLastChangeOn($createdDate);
        $this->setLastChangeBy($createdBy);
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
    public function getAssociationList()
    {
        return $this->associationList;
    }

    /**
     *
     * @return mixed
     */
    public function getBackwardAssociationList()
    {
        return $this->backwardAssociationList;
    }

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

    /**
     *
     * @return mixed
     */
    public function getStatistics()
    {
        return $this->statistics;
    }

    /**
     *
     * @param mixed $assetLabel1
     */
    public function setAssetLabel1($assetLabel1)
    {
        $this->assetLabel1 = $assetLabel1;
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
     * @param mixed $associationList
     */
    public function setAssociationList($associationList)
    {
        $this->associationList = $associationList;
    }

    /**
     *
     * @param mixed $backwardAssociationList
     */
    public function setBackwardAssociationList($backwardAssociationList)
    {
        $this->backwardAssociationList = $backwardAssociationList;
    }

    /**
     *
     * @param mixed $qoList
     */
    public function setQoList($qoList)
    {
        $this->qoList = $qoList;
    }

    /**
     *
     * @param mixed $procureGrList
     */
    public function setProcureGrList($procureGrList)
    {
        $this->procureGrList = $procureGrList;
    }

    /**
     *
     * @param mixed $batchNoList
     */
    public function setBatchNoList($batchNoList)
    {
        $this->batchNoList = $batchNoList;
    }

    /**
     *
     * @param mixed $fifoLayerConsumeList
     */
    public function setFifoLayerConsumeList($fifoLayerConsumeList)
    {
        $this->fifoLayerConsumeList = $fifoLayerConsumeList;
    }

    /**
     *
     * @param mixed $stockGrList
     */
    public function setStockGrList($stockGrList)
    {
        $this->stockGrList = $stockGrList;
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
     * @param mixed $onHandValue
     */
    public function setOnHandValue($onHandValue)
    {
        $this->onHandValue = $onHandValue;
    }

    /**
     *
     * @param mixed $standardUnitName
     */
    public function setStandardUnitName($standardUnitName)
    {
        $this->standardUnitName = $standardUnitName;
    }

    /**
     *
     * @param mixed $statistics
     */
    public function setStatistics($statistics)
    {
        $this->statistics = $statistics;
    }

    /**
     *
     * @return mixed
     */
    public function getVariantCollection()
    {
        return $this->variantCollection;
    }

    /**
     *
     * @param mixed $variantCollection
     */
    public function setVariantCollection($variantCollection)
    {
        $this->variantCollection = $variantCollection;
    }
}
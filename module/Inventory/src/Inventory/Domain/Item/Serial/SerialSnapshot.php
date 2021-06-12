<?php
namespace Inventory\Domain\Item\Serial;

use Application\Domain\Shared\AbstractDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class SerialSnapshot extends AbstractDTO
{

    public $id;

    public $token;

    public $serialNumber;

    public $isActive;

    public $remarks;

    public $createdOn;

    public $consumedOn;

    public $mfgSerialNumber;

    public $mfgDate;

    public $lotNumber;

    public $mfgWarrantyStart;

    public $mfgWarrantyEnd;

    public $itemName;

    public $location;

    public $category;

    public $mfgName;

    public $lastchangeOn;

    public $revisionNo;

    public $sysNumber;

    public $serialNumber1;

    public $serialNumber2;

    public $serialNumber3;

    public $mfgModel;

    public $mfgModel1;

    public $mfgModel2;

    public $mfgDescription;

    public $capacity;

    public $erpAssetNumber;

    public $erpAssetNumber1;

    public $isReversed;

    public $reversalDate;

    public $reversalDoc;

    public $reversalReason;

    public $isReversable;

    public $uuid;

    public $createdBy;

    public $lastchangeBy;

    public $item;

    public $serial;

    public $inventoryTrx;

    public $apRow;

    public $grRow;

    public $originCountry;

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     *
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialNumber()
    {
        return $this->serialNumber;
    }

    /**
     *
     * @param mixed $serialNumber
     */
    public function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;
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
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @return mixed
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     *
     * @param mixed $remarks
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     *
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @return mixed
     */
    public function getConsumedOn()
    {
        return $this->consumedOn;
    }

    /**
     *
     * @param mixed $consumedOn
     */
    public function setConsumedOn($consumedOn)
    {
        $this->consumedOn = $consumedOn;
    }

    /**
     *
     * @return mixed
     */
    public function getMfgSerialNumber()
    {
        return $this->mfgSerialNumber;
    }

    /**
     *
     * @param mixed $mfgSerialNumber
     */
    public function setMfgSerialNumber($mfgSerialNumber)
    {
        $this->mfgSerialNumber = $mfgSerialNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getMfgDate()
    {
        return $this->mfgDate;
    }

    /**
     *
     * @param mixed $mfgDate
     */
    public function setMfgDate($mfgDate)
    {
        $this->mfgDate = $mfgDate;
    }

    /**
     *
     * @return mixed
     */
    public function getLotNumber()
    {
        return $this->lotNumber;
    }

    /**
     *
     * @param mixed $lotNumber
     */
    public function setLotNumber($lotNumber)
    {
        $this->lotNumber = $lotNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getMfgWarrantyStart()
    {
        return $this->mfgWarrantyStart;
    }

    /**
     *
     * @param mixed $mfgWarrantyStart
     */
    public function setMfgWarrantyStart($mfgWarrantyStart)
    {
        $this->mfgWarrantyStart = $mfgWarrantyStart;
    }

    /**
     *
     * @return mixed
     */
    public function getMfgWarrantyEnd()
    {
        return $this->mfgWarrantyEnd;
    }

    /**
     *
     * @param mixed $mfgWarrantyEnd
     */
    public function setMfgWarrantyEnd($mfgWarrantyEnd)
    {
        $this->mfgWarrantyEnd = $mfgWarrantyEnd;
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
     * @param mixed $itemName
     */
    public function setItemName($itemName)
    {
        $this->itemName = $itemName;
    }

    /**
     *
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     *
     * @param mixed $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     *
     * @return mixed
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     *
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     *
     * @return mixed
     */
    public function getMfgName()
    {
        return $this->mfgName;
    }

    /**
     *
     * @param mixed $mfgName
     */
    public function setMfgName($mfgName)
    {
        $this->mfgName = $mfgName;
    }

    /**
     *
     * @return mixed
     */
    public function getLastchangeOn()
    {
        return $this->lastchangeOn;
    }

    /**
     *
     * @param mixed $lastchangeOn
     */
    public function setLastchangeOn($lastchangeOn)
    {
        $this->lastchangeOn = $lastchangeOn;
    }

    /**
     *
     * @return mixed
     */
    public function getRevisionNo()
    {
        return $this->revisionNo;
    }

    /**
     *
     * @param mixed $revisionNo
     */
    public function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;
    }

    /**
     *
     * @return mixed
     */
    public function getSysNumber()
    {
        return $this->sysNumber;
    }

    /**
     *
     * @param mixed $sysNumber
     */
    public function setSysNumber($sysNumber)
    {
        $this->sysNumber = $sysNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialNumber1()
    {
        return $this->serialNumber1;
    }

    /**
     *
     * @param mixed $serialNumber1
     */
    public function setSerialNumber1($serialNumber1)
    {
        $this->serialNumber1 = $serialNumber1;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialNumber2()
    {
        return $this->serialNumber2;
    }

    /**
     *
     * @param mixed $serialNumber2
     */
    public function setSerialNumber2($serialNumber2)
    {
        $this->serialNumber2 = $serialNumber2;
    }

    /**
     *
     * @return mixed
     */
    public function getSerialNumber3()
    {
        return $this->serialNumber3;
    }

    /**
     *
     * @param mixed $serialNumber3
     */
    public function setSerialNumber3($serialNumber3)
    {
        $this->serialNumber3 = $serialNumber3;
    }

    /**
     *
     * @return mixed
     */
    public function getMfgModel()
    {
        return $this->mfgModel;
    }

    /**
     *
     * @param mixed $mfgModel
     */
    public function setMfgModel($mfgModel)
    {
        $this->mfgModel = $mfgModel;
    }

    /**
     *
     * @return mixed
     */
    public function getMfgModel1()
    {
        return $this->mfgModel1;
    }

    /**
     *
     * @param mixed $mfgModel1
     */
    public function setMfgModel1($mfgModel1)
    {
        $this->mfgModel1 = $mfgModel1;
    }

    /**
     *
     * @return mixed
     */
    public function getMfgModel2()
    {
        return $this->mfgModel2;
    }

    /**
     *
     * @param mixed $mfgModel2
     */
    public function setMfgModel2($mfgModel2)
    {
        $this->mfgModel2 = $mfgModel2;
    }

    /**
     *
     * @return mixed
     */
    public function getMfgDescription()
    {
        return $this->mfgDescription;
    }

    /**
     *
     * @param mixed $mfgDescription
     */
    public function setMfgDescription($mfgDescription)
    {
        $this->mfgDescription = $mfgDescription;
    }

    /**
     *
     * @return mixed
     */
    public function getCapacity()
    {
        return $this->capacity;
    }

    /**
     *
     * @param mixed $capacity
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
    }

    /**
     *
     * @return mixed
     */
    public function getErpAssetNumber()
    {
        return $this->erpAssetNumber;
    }

    /**
     *
     * @param mixed $erpAssetNumber
     */
    public function setErpAssetNumber($erpAssetNumber)
    {
        $this->erpAssetNumber = $erpAssetNumber;
    }

    /**
     *
     * @return mixed
     */
    public function getErpAssetNumber1()
    {
        return $this->erpAssetNumber1;
    }

    /**
     *
     * @param mixed $erpAssetNumber1
     */
    public function setErpAssetNumber1($erpAssetNumber1)
    {
        $this->erpAssetNumber1 = $erpAssetNumber1;
    }

    /**
     *
     * @return mixed
     */
    public function getIsReversed()
    {
        return $this->isReversed;
    }

    /**
     *
     * @param mixed $isReversed
     */
    public function setIsReversed($isReversed)
    {
        $this->isReversed = $isReversed;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalDate()
    {
        return $this->reversalDate;
    }

    /**
     *
     * @param mixed $reversalDate
     */
    public function setReversalDate($reversalDate)
    {
        $this->reversalDate = $reversalDate;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalDoc()
    {
        return $this->reversalDoc;
    }

    /**
     *
     * @param mixed $reversalDoc
     */
    public function setReversalDoc($reversalDoc)
    {
        $this->reversalDoc = $reversalDoc;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalReason()
    {
        return $this->reversalReason;
    }

    /**
     *
     * @param mixed $reversalReason
     */
    public function setReversalReason($reversalReason)
    {
        $this->reversalReason = $reversalReason;
    }

    /**
     *
     * @return mixed
     */
    public function getIsReversable()
    {
        return $this->isReversable;
    }

    /**
     *
     * @param mixed $isReversable
     */
    public function setIsReversable($isReversable)
    {
        $this->isReversable = $isReversable;
    }

    /**
     *
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     *
     * @param mixed $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     *
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @return mixed
     */
    public function getLastchangeBy()
    {
        return $this->lastchangeBy;
    }

    /**
     *
     * @param mixed $lastchangeBy
     */
    public function setLastchangeBy($lastchangeBy)
    {
        $this->lastchangeBy = $lastchangeBy;
    }

    /**
     *
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
    }

    /**
     *
     * @param mixed $item
     */
    public function setItem($item)
    {
        $this->item = $item;
    }

    /**
     *
     * @return mixed
     */
    public function getSerial()
    {
        return $this->serial;
    }

    /**
     *
     * @param mixed $serial
     */
    public function setSerial($serial)
    {
        $this->serial = $serial;
    }

    /**
     *
     * @return mixed
     */
    public function getInventoryTrx()
    {
        return $this->inventoryTrx;
    }

    /**
     *
     * @param mixed $inventoryTrx
     */
    public function setInventoryTrx($inventoryTrx)
    {
        $this->inventoryTrx = $inventoryTrx;
    }

    /**
     *
     * @return mixed
     */
    public function getApRow()
    {
        return $this->apRow;
    }

    /**
     *
     * @param mixed $apRow
     */
    public function setApRow($apRow)
    {
        $this->apRow = $apRow;
    }

    /**
     *
     * @return mixed
     */
    public function getGrRow()
    {
        return $this->grRow;
    }

    /**
     *
     * @param mixed $grRow
     */
    public function setGrRow($grRow)
    {
        $this->grRow = $grRow;
    }

    /**
     *
     * @return mixed
     */
    public function getOriginCountry()
    {
        return $this->originCountry;
    }

    /**
     *
     * @param mixed $originCountry
     */
    public function setOriginCountry($originCountry)
    {
        $this->originCountry = $originCountry;
    }
}
<?php
namespace Inventory\Domain\Item\Serial;

use Application\Domain\Shared\AbstractEntity;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AbstractSerial extends AbstractEntity
{

    protected $id;

    protected $token;

    protected $serialNumber;

    protected $isActive;

    protected $remarks;

    protected $createdOn;

    protected $consumedOn;

    protected $mfgSerialNumber;

    protected $mfgDate;

    protected $lotNumber;

    protected $mfgWarrantyStart;

    protected $mfgWarrantyEnd;

    protected $itemName;

    protected $location;

    protected $category;

    protected $mfgName;

    protected $lastchangeOn;

    protected $revisionNo;

    protected $sysNumber;

    protected $serialNumber1;

    protected $serialNumber2;

    protected $serialNumber3;

    protected $mfgModel;

    protected $mfgModel1;

    protected $mfgModel2;

    protected $mfgDescription;

    protected $capacity;

    protected $erpAssetNumber;

    protected $erpAssetNumber1;

    protected $isReversed;

    protected $reversalDate;

    protected $reversalDoc;

    protected $reversalReason;

    protected $isReversable;

    protected $uuid;

    protected $createdBy;

    protected $lastchangeBy;

    protected $item;

    protected $serial;

    protected $inventoryTrx;

    protected $apRow;

    protected $grRow;

    protected $originCountry;

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
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
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
     * @return mixed
     */
    public function getIsActive()
    {
        return $this->isActive;
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
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
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
     * @return mixed
     */
    public function getMfgSerialNumber()
    {
        return $this->mfgSerialNumber;
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
     * @return mixed
     */
    public function getLotNumber()
    {
        return $this->lotNumber;
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
     * @return mixed
     */
    public function getMfgWarrantyEnd()
    {
        return $this->mfgWarrantyEnd;
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
    public function getLocation()
    {
        return $this->location;
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
     * @return mixed
     */
    public function getMfgName()
    {
        return $this->mfgName;
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
     * @return mixed
     */
    public function getRevisionNo()
    {
        return $this->revisionNo;
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
     * @return mixed
     */
    public function getSerialNumber1()
    {
        return $this->serialNumber1;
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
     * @return mixed
     */
    public function getSerialNumber3()
    {
        return $this->serialNumber3;
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
     * @return mixed
     */
    public function getMfgModel1()
    {
        return $this->mfgModel1;
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
     * @return mixed
     */
    public function getMfgDescription()
    {
        return $this->mfgDescription;
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
     * @return mixed
     */
    public function getErpAssetNumber()
    {
        return $this->erpAssetNumber;
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
     * @return mixed
     */
    public function getIsReversed()
    {
        return $this->isReversed;
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
     * @return mixed
     */
    public function getReversalDoc()
    {
        return $this->reversalDoc;
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
     * @return mixed
     */
    public function getIsReversable()
    {
        return $this->isReversable;
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
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
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
     * @return mixed
     */
    public function getItem()
    {
        return $this->item;
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
     * @return mixed
     */
    public function getInventoryTrx()
    {
        return $this->inventoryTrx;
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
     * @return mixed
     */
    public function getGrRow()
    {
        return $this->grRow;
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
     * @param mixed $id
     */
    protected function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param mixed $token
     */
    protected function setToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     * @param mixed $serialNumber
     */
    protected function setSerialNumber($serialNumber)
    {
        $this->serialNumber = $serialNumber;
    }

    /**
     *
     * @param mixed $isActive
     */
    protected function setIsActive($isActive)
    {
        $this->isActive = $isActive;
    }

    /**
     *
     * @param mixed $remarks
     */
    protected function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     *
     * @param mixed $createdOn
     */
    protected function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @param mixed $consumedOn
     */
    protected function setConsumedOn($consumedOn)
    {
        $this->consumedOn = $consumedOn;
    }

    /**
     *
     * @param mixed $mfgSerialNumber
     */
    protected function setMfgSerialNumber($mfgSerialNumber)
    {
        $this->mfgSerialNumber = $mfgSerialNumber;
    }

    /**
     *
     * @param mixed $mfgDate
     */
    protected function setMfgDate($mfgDate)
    {
        $this->mfgDate = $mfgDate;
    }

    /**
     *
     * @param mixed $lotNumber
     */
    protected function setLotNumber($lotNumber)
    {
        $this->lotNumber = $lotNumber;
    }

    /**
     *
     * @param mixed $mfgWarrantyStart
     */
    protected function setMfgWarrantyStart($mfgWarrantyStart)
    {
        $this->mfgWarrantyStart = $mfgWarrantyStart;
    }

    /**
     *
     * @param mixed $mfgWarrantyEnd
     */
    protected function setMfgWarrantyEnd($mfgWarrantyEnd)
    {
        $this->mfgWarrantyEnd = $mfgWarrantyEnd;
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
     * @param mixed $location
     */
    protected function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     *
     * @param mixed $category
     */
    protected function setCategory($category)
    {
        $this->category = $category;
    }

    /**
     *
     * @param mixed $mfgName
     */
    protected function setMfgName($mfgName)
    {
        $this->mfgName = $mfgName;
    }

    /**
     *
     * @param mixed $lastchangeOn
     */
    protected function setLastchangeOn($lastchangeOn)
    {
        $this->lastchangeOn = $lastchangeOn;
    }

    /**
     *
     * @param mixed $revisionNo
     */
    protected function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;
    }

    /**
     *
     * @param mixed $sysNumber
     */
    protected function setSysNumber($sysNumber)
    {
        $this->sysNumber = $sysNumber;
    }

    /**
     *
     * @param mixed $serialNumber1
     */
    protected function setSerialNumber1($serialNumber1)
    {
        $this->serialNumber1 = $serialNumber1;
    }

    /**
     *
     * @param mixed $serialNumber2
     */
    protected function setSerialNumber2($serialNumber2)
    {
        $this->serialNumber2 = $serialNumber2;
    }

    /**
     *
     * @param mixed $serialNumber3
     */
    protected function setSerialNumber3($serialNumber3)
    {
        $this->serialNumber3 = $serialNumber3;
    }

    /**
     *
     * @param mixed $mfgModel
     */
    protected function setMfgModel($mfgModel)
    {
        $this->mfgModel = $mfgModel;
    }

    /**
     *
     * @param mixed $mfgModel1
     */
    protected function setMfgModel1($mfgModel1)
    {
        $this->mfgModel1 = $mfgModel1;
    }

    /**
     *
     * @param mixed $mfgModel2
     */
    protected function setMfgModel2($mfgModel2)
    {
        $this->mfgModel2 = $mfgModel2;
    }

    /**
     *
     * @param mixed $mfgDescription
     */
    protected function setMfgDescription($mfgDescription)
    {
        $this->mfgDescription = $mfgDescription;
    }

    /**
     *
     * @param mixed $capacity
     */
    protected function setCapacity($capacity)
    {
        $this->capacity = $capacity;
    }

    /**
     *
     * @param mixed $erpAssetNumber
     */
    protected function setErpAssetNumber($erpAssetNumber)
    {
        $this->erpAssetNumber = $erpAssetNumber;
    }

    /**
     *
     * @param mixed $erpAssetNumber1
     */
    protected function setErpAssetNumber1($erpAssetNumber1)
    {
        $this->erpAssetNumber1 = $erpAssetNumber1;
    }

    /**
     *
     * @param mixed $isReversed
     */
    protected function setIsReversed($isReversed)
    {
        $this->isReversed = $isReversed;
    }

    /**
     *
     * @param mixed $reversalDate
     */
    protected function setReversalDate($reversalDate)
    {
        $this->reversalDate = $reversalDate;
    }

    /**
     *
     * @param mixed $reversalDoc
     */
    protected function setReversalDoc($reversalDoc)
    {
        $this->reversalDoc = $reversalDoc;
    }

    /**
     *
     * @param mixed $reversalReason
     */
    protected function setReversalReason($reversalReason)
    {
        $this->reversalReason = $reversalReason;
    }

    /**
     *
     * @param mixed $isReversable
     */
    protected function setIsReversable($isReversable)
    {
        $this->isReversable = $isReversable;
    }

    /**
     *
     * @param mixed $uuid
     */
    protected function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     *
     * @param mixed $createdBy
     */
    protected function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @param mixed $lastchangeBy
     */
    protected function setLastchangeBy($lastchangeBy)
    {
        $this->lastchangeBy = $lastchangeBy;
    }

    /**
     *
     * @param mixed $item
     */
    protected function setItem($item)
    {
        $this->item = $item;
    }

    /**
     *
     * @param mixed $serial
     */
    protected function setSerial($serial)
    {
        $this->serial = $serial;
    }

    /**
     *
     * @param mixed $inventoryTrx
     */
    protected function setInventoryTrx($inventoryTrx)
    {
        $this->inventoryTrx = $inventoryTrx;
    }

    /**
     *
     * @param mixed $apRow
     */
    protected function setApRow($apRow)
    {
        $this->apRow = $apRow;
    }

    /**
     *
     * @param mixed $grRow
     */
    protected function setGrRow($grRow)
    {
        $this->grRow = $grRow;
    }

    /**
     *
     * @param mixed $originCountry
     */
    protected function setOriginCountry($originCountry)
    {
        $this->originCountry = $originCountry;
    }
}

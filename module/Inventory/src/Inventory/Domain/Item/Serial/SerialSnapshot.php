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

    /*
     * |=============================
     * | Inventory\Domain\Item\Serial\BaseSerial
     * |
     * |=============================
     */
    public $invoiceSysNumber;

    public $vendorName;

    public $invoiceId;

    public $invoiceToken;

    public $grSysNumber;

    public $grId;

    public $grToken;

    public $itemName;

    public $itemToken;

    /*
     * |=============================
     * | Inventory\Domain\Item\Serial\AbstractSerial
     * |
     * |=============================
     */
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
     * @param mixed $invoiceSysNumber
     */
    public function setInvoiceSysNumber($invoiceSysNumber)
    {
        $this->invoiceSysNumber = $invoiceSysNumber;
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
     * @param mixed $invoiceToken
     */
    public function setInvoiceToken($invoiceToken)
    {
        $this->invoiceToken = $invoiceToken;
    }

    /**
     *
     * @param mixed $grSysNumber
     */
    public function setGrSysNumber($grSysNumber)
    {
        $this->grSysNumber = $grSysNumber;
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
     * @param mixed $grToken
     */
    public function setGrToken($grToken)
    {
        $this->grToken = $grToken;
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
     * @param mixed $itemToken
     */
    public function setItemToken($itemToken)
    {
        $this->itemToken = $itemToken;
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
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
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
     * @param mixed $isActive
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;
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
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
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
     * @param mixed $mfgSerialNumber
     */
    public function setMfgSerialNumber($mfgSerialNumber)
    {
        $this->mfgSerialNumber = $mfgSerialNumber;
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
     * @param mixed $lotNumber
     */
    public function setLotNumber($lotNumber)
    {
        $this->lotNumber = $lotNumber;
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
     * @param mixed $mfgWarrantyEnd
     */
    public function setMfgWarrantyEnd($mfgWarrantyEnd)
    {
        $this->mfgWarrantyEnd = $mfgWarrantyEnd;
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
     * @param mixed $category
     */
    public function setCategory($category)
    {
        $this->category = $category;
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
     * @param mixed $lastchangeOn
     */
    public function setLastchangeOn($lastchangeOn)
    {
        $this->lastchangeOn = $lastchangeOn;
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
     * @param mixed $sysNumber
     */
    public function setSysNumber($sysNumber)
    {
        $this->sysNumber = $sysNumber;
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
     * @param mixed $serialNumber2
     */
    public function setSerialNumber2($serialNumber2)
    {
        $this->serialNumber2 = $serialNumber2;
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
     * @param mixed $mfgModel
     */
    public function setMfgModel($mfgModel)
    {
        $this->mfgModel = $mfgModel;
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
     * @param mixed $mfgModel2
     */
    public function setMfgModel2($mfgModel2)
    {
        $this->mfgModel2 = $mfgModel2;
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
     * @param mixed $capacity
     */
    public function setCapacity($capacity)
    {
        $this->capacity = $capacity;
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
     * @param mixed $erpAssetNumber1
     */
    public function setErpAssetNumber1($erpAssetNumber1)
    {
        $this->erpAssetNumber1 = $erpAssetNumber1;
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
     * @param mixed $reversalDate
     */
    public function setReversalDate($reversalDate)
    {
        $this->reversalDate = $reversalDate;
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
     * @param mixed $reversalReason
     */
    public function setReversalReason($reversalReason)
    {
        $this->reversalReason = $reversalReason;
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
     * @param mixed $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
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
     * @param mixed $lastchangeBy
     */
    public function setLastchangeBy($lastchangeBy)
    {
        $this->lastchangeBy = $lastchangeBy;
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
     * @param mixed $serial
     */
    public function setSerial($serial)
    {
        $this->serial = $serial;
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
     * @param mixed $apRow
     */
    public function setApRow($apRow)
    {
        $this->apRow = $apRow;
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
     * @param mixed $originCountry
     */
    public function setOriginCountry($originCountry)
    {
        $this->originCountry = $originCountry;
    }

    /**
     *
     * @return mixed
     */
    public function getVendorName()
    {
        return $this->vendorName;
    }
}
<?php
namespace Inventory\Domain\Item\Variant;

use Application\Domain\Shared\AbstractDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class VariantSnapshot extends AbstractDTO
{

    public $id;

    public $uuid;

    public $combinedName;

    public $createdOn;

    public $lastChangeOn;

    public $price;

    public $isActive;

    public $upc;

    public $ean13;

    public $barcode;

    public $weight;

    public $remarks;

    public $version;

    public $revisionNo;

    public $cbm;

    public $variantCode;

    public $variantName;

    public $variantAlias;

    public $sysNumber;

    public $item;

    public $createdBy;

    public $lastChangeBy;

    public $fullCombinedName;

    public $itemName;

    public $variantSku;

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
    public function getCombinedName()
    {
        return $this->combinedName;
    }

    /**
     *
     * @param mixed $combinedName
     */
    public function setCombinedName($combinedName)
    {
        $this->combinedName = $combinedName;
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
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     *
     * @param mixed $lastChangeOn
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
    }

    /**
     *
     * @return mixed
     */
    public function getPrice()
    {
        return $this->price;
    }

    /**
     *
     * @param mixed $price
     */
    public function setPrice($price)
    {
        $this->price = $price;
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
    public function getUpc()
    {
        return $this->upc;
    }

    /**
     *
     * @param mixed $upc
     */
    public function setUpc($upc)
    {
        $this->upc = $upc;
    }

    /**
     *
     * @return mixed
     */
    public function getEan13()
    {
        return $this->ean13;
    }

    /**
     *
     * @param mixed $ean13
     */
    public function setEan13($ean13)
    {
        $this->ean13 = $ean13;
    }

    /**
     *
     * @return mixed
     */
    public function getBarcode()
    {
        return $this->barcode;
    }

    /**
     *
     * @param mixed $barcode
     */
    public function setBarcode($barcode)
    {
        $this->barcode = $barcode;
    }

    /**
     *
     * @return mixed
     */
    public function getWeight()
    {
        return $this->weight;
    }

    /**
     *
     * @param mixed $weight
     */
    public function setWeight($weight)
    {
        $this->weight = $weight;
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
    public function getVersion()
    {
        return $this->version;
    }

    /**
     *
     * @param mixed $version
     */
    public function setVersion($version)
    {
        $this->version = $version;
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
    public function getCbm()
    {
        return $this->cbm;
    }

    /**
     *
     * @param mixed $cbm
     */
    public function setCbm($cbm)
    {
        $this->cbm = $cbm;
    }

    /**
     *
     * @return mixed
     */
    public function getVariantCode()
    {
        return $this->variantCode;
    }

    /**
     *
     * @param mixed $variantCode
     */
    public function setVariantCode($variantCode)
    {
        $this->variantCode = $variantCode;
    }

    /**
     *
     * @return mixed
     */
    public function getVariantName()
    {
        return $this->variantName;
    }

    /**
     *
     * @param mixed $variantName
     */
    public function setVariantName($variantName)
    {
        $this->variantName = $variantName;
    }

    /**
     *
     * @return mixed
     */
    public function getVariantAlias()
    {
        return $this->variantAlias;
    }

    /**
     *
     * @param mixed $variantAlias
     */
    public function setVariantAlias($variantAlias)
    {
        $this->variantAlias = $variantAlias;
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
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    /**
     *
     * @param mixed $lastChangeBy
     */
    public function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
    }

    /**
     *
     * @return mixed
     */
    public function getFullCombinedName()
    {
        return $this->fullCombinedName;
    }

    /**
     *
     * @param mixed $fullCombinedName
     */
    public function setFullCombinedName($fullCombinedName)
    {
        $this->fullCombinedName = $fullCombinedName;
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
    public function getVariantSku()
    {
        return $this->variantSku;
    }

    /**
     *
     * @param mixed $variantSku
     */
    public function setVariantSku($variantSku)
    {
        $this->variantSku = $variantSku;
    }
}
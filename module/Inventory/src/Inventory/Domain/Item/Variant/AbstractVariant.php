<?php
namespace Inventory\Domain\Item\Variant;

use Application\Domain\Shared\AbstractEntity;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AbstractVariant extends AbstractEntity
{

    protected $id;

    protected $uuid;

    protected $combinedName;

    protected $createdOn;

    protected $lastChangeOn;

    protected $price;

    protected $isActive;

    protected $upc;

    protected $ean13;

    protected $barcode;

    protected $weight;

    protected $remarks;

    protected $version;

    protected $revisionNo;

    protected $cbm;

    protected $variantCode;

    protected $variantName;

    protected $variantAlias;

    protected $sysNumber;

    protected $item;

    protected $createdBy;

    protected $lastChangeBy;

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
    public function getUuid()
    {
        return $this->uuid;
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
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
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
    public function getUpc()
    {
        return $this->upc;
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
     * @return mixed
     */
    public function getBarcode()
    {
        return $this->barcode;
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
    public function getVersion()
    {
        return $this->version;
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
    public function getCbm()
    {
        return $this->cbm;
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
     * @return mixed
     */
    public function getVariantName()
    {
        return $this->variantName;
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
    public function getItem()
    {
        return $this->item;
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
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
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
     * @param mixed $uuid
     */
    protected function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     *
     * @param mixed $combinedName
     */
    protected function setCombinedName($combinedName)
    {
        $this->combinedName = $combinedName;
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
     * @param mixed $lastChangeOn
     */
    protected function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
    }

    /**
     *
     * @param mixed $price
     */
    protected function setPrice($price)
    {
        $this->price = $price;
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
     * @param mixed $upc
     */
    protected function setUpc($upc)
    {
        $this->upc = $upc;
    }

    /**
     *
     * @param mixed $ean13
     */
    protected function setEan13($ean13)
    {
        $this->ean13 = $ean13;
    }

    /**
     *
     * @param mixed $barcode
     */
    protected function setBarcode($barcode)
    {
        $this->barcode = $barcode;
    }

    /**
     *
     * @param mixed $weight
     */
    protected function setWeight($weight)
    {
        $this->weight = $weight;
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
     * @param mixed $version
     */
    protected function setVersion($version)
    {
        $this->version = $version;
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
     * @param mixed $cbm
     */
    protected function setCbm($cbm)
    {
        $this->cbm = $cbm;
    }

    /**
     *
     * @param mixed $variantCode
     */
    protected function setVariantCode($variantCode)
    {
        $this->variantCode = $variantCode;
    }

    /**
     *
     * @param mixed $variantName
     */
    protected function setVariantName($variantName)
    {
        $this->variantName = $variantName;
    }

    /**
     *
     * @param mixed $variantAlias
     */
    protected function setVariantAlias($variantAlias)
    {
        $this->variantAlias = $variantAlias;
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
     * @param mixed $item
     */
    protected function setItem($item)
    {
        $this->item = $item;
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
     * @param mixed $lastChangeBy
     */
    protected function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
    }
}

<?php
namespace Inventory\Domain\Item\Component;

use Application\Domain\Shared\AbstractDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ComponentSnapshot extends AbstractDTO
{

    public $id;

    public $token;

    public $uuid;

    public $createdBy;

    public $lastChangeBy;

    public $createdOn;

    public $lastChangeOn;

    public $quantity;

    public $uom;

    public $price;

    public $remarks;

    public $hasMember;

    public $parentUuid;

    public $parent;

    public $item;

    public $revisionNo;

    public $version;

    /**
     *
     * @return mixed
     */
    protected function getId()
    {
        return $this->id;
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
     * @return mixed
     */
    protected function getToken()
    {
        return $this->token;
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
     * @return mixed
     */
    protected function getUuid()
    {
        return $this->uuid;
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
     * @return mixed
     */
    protected function getCreatedBy()
    {
        return $this->createdBy;
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
     * @return mixed
     */
    protected function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    /**
     *
     * @param mixed $lastChangeBy
     */
    protected function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
    }

    /**
     *
     * @return mixed
     */
    protected function getCreatedOn()
    {
        return $this->createdOn;
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
     * @return mixed
     */
    protected function getLastChangeOn()
    {
        return $this->lastChangeOn;
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
     * @return mixed
     */
    protected function getQuantity()
    {
        return $this->quantity;
    }

    /**
     *
     * @param mixed $quantity
     */
    protected function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     *
     * @return mixed
     */
    protected function getUom()
    {
        return $this->uom;
    }

    /**
     *
     * @param mixed $uom
     */
    protected function setUom($uom)
    {
        $this->uom = $uom;
    }

    /**
     *
     * @return mixed
     */
    protected function getPrice()
    {
        return $this->price;
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
     * @return mixed
     */
    protected function getRemarks()
    {
        return $this->remarks;
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
     * @return mixed
     */
    protected function getHasMember()
    {
        return $this->hasMember;
    }

    /**
     *
     * @param mixed $hasMember
     */
    protected function setHasMember($hasMember)
    {
        $this->hasMember = $hasMember;
    }

    /**
     *
     * @return mixed
     */
    protected function getParentUuid()
    {
        return $this->parentUuid;
    }

    /**
     *
     * @param mixed $parentUuid
     */
    protected function setParentUuid($parentUuid)
    {
        $this->parentUuid = $parentUuid;
    }

    /**
     *
     * @return mixed
     */
    protected function getParent()
    {
        return $this->parent;
    }

    /**
     *
     * @param mixed $parent
     */
    protected function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     *
     * @return mixed
     */
    protected function getItem()
    {
        return $this->item;
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
     * @return mixed
     */
    protected function getRevisionNo()
    {
        return $this->revisionNo;
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
     * @return mixed
     */
    protected function getVersion()
    {
        return $this->version;
    }

    /**
     *
     * @param mixed $version
     */
    protected function setVersion($version)
    {
        $this->version = $version;
    }
}
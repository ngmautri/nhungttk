<?php
namespace Inventory\Domain\Item\Composite;

use Application\Domain\Shared\AbstractEntity;
use Application\Domain\Shared\AggregateRootInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractComposite extends AbstractEntity implements AggregateRootInterface
{

    protected $id;

    protected $token;

    protected $uuid;

    protected $createdBy;

    protected $lastChangeBy;

    protected $createdOn;

    protected $lastChangeOn;

    protected $quantity;

    protected $uom;

    protected $price;

    protected $remarks;

    protected $hasMember;

    protected $parentUuid;

    protected $parent;

    protected $item;

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
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
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
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     *
     * @return mixed
     */
    public function getUom()
    {
        return $this->uom;
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
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     *
     * @return mixed
     */
    public function getHasMember()
    {
        return $this->hasMember;
    }

    /**
     *
     * @return mixed
     */
    public function getParentUuid()
    {
        return $this->parentUuid;
    }

    /**
     *
     * @return mixed
     */
    public function getParent()
    {
        return $this->parent;
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
     * @param mixed $lastChangeBy
     */
    protected function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
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
     * @param mixed $quantity
     */
    protected function setQuantity($quantity)
    {
        $this->quantity = $quantity;
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
     * @param mixed $price
     */
    protected function setPrice($price)
    {
        $this->price = $price;
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
     * @param mixed $hasMember
     */
    protected function setHasMember($hasMember)
    {
        $this->hasMember = $hasMember;
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
     * @param mixed $parent
     */
    protected function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     *
     * @param mixed $item
     */
    protected function setItem($item)
    {
        $this->item = $item;
    }
}
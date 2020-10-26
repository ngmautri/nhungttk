<?php
namespace Application\Domain\Shared\Uom;

use Application\Domain\Shared\AbstractDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UomPairSnapshot extends AbstractDTO
{

    public $id;

    public $pairName;

    public $baseUom;

    public $counterUom;

    public $convertFactor;

    public $description;

    public $isActive;

    public $remarks;

    public $createdOn;

    public $lastChangeOn;

    public $groupName;

    public $group;

    public $createdBy;

    public $lastChangeBy;

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
    public function getPairName()
    {
        return $this->pairName;
    }

    /**
     *
     * @return mixed
     */
    public function getBaseUom()
    {
        return $this->baseUom;
    }

    /**
     *
     * @return mixed
     */
    public function getCounterUom()
    {
        return $this->counterUom;
    }

    /**
     *
     * @return mixed
     */
    public function getConvertFactor()
    {
        return $this->convertFactor;
    }

    /**
     *
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
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
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     *
     * @return mixed
     */
    public function getGroupName()
    {
        return $this->groupName;
    }

    /**
     *
     * @return mixed
     */
    public function getGroup()
    {
        return $this->group;
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
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param mixed $pairName
     */
    public function setPairName($pairName)
    {
        $this->pairName = $pairName;
    }

    /**
     *
     * @param mixed $baseUom
     */
    public function setBaseUom($baseUom)
    {
        $this->baseUom = $baseUom;
    }

    /**
     *
     * @param mixed $counterUom
     */
    public function setCounterUom($counterUom)
    {
        $this->counterUom = $counterUom;
    }

    /**
     *
     * @param mixed $convertFactor
     */
    public function setConvertFactor($convertFactor)
    {
        $this->convertFactor = $convertFactor;
    }

    /**
     *
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
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
     * @param mixed $lastChangeOn
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
    }

    /**
     *
     * @param mixed $groupName
     */
    public function setGroupName($groupName)
    {
        $this->groupName = $groupName;
    }

    /**
     *
     * @param mixed $group
     */
    public function setGroup($group)
    {
        $this->group = $group;
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
     * @param mixed $lastChangeBy
     */
    public function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
    }
}

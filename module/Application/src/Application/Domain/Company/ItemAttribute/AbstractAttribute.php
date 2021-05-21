<?php
namespace Application\Domain\Company\ItemAttribute;

use Application\Domain\Shared\AbstractEntity;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AbstractAttribute extends AbstractEntity
{

    protected $id;

    protected $uuid;

    protected $attributeCode;

    protected $attributeName;

    protected $attributeName1;

    protected $attributeName2;

    protected $combinedName;

    protected $createdOn;

    protected $lastChangeOn;

    protected $sysNumber;

    protected $version;

    protected $revisionNo;

    protected $remarks;

    protected $group;

    protected $createdBy;

    protected $lastChangeBy;

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
     * @param mixed $attributeCode
     */
    protected function setAttributeCode($attributeCode)
    {
        $this->attributeCode = $attributeCode;
    }

    /**
     *
     * @param mixed $attributeName
     */
    protected function setAttributeName($attributeName)
    {
        $this->attributeName = $attributeName;
    }

    /**
     *
     * @param mixed $attributeName1
     */
    protected function setAttributeName1($attributeName1)
    {
        $this->attributeName1 = $attributeName1;
    }

    /**
     *
     * @param mixed $attributeName2
     */
    protected function setAttributeName2($attributeName2)
    {
        $this->attributeName2 = $attributeName2;
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
     * @param mixed $sysNumber
     */
    protected function setSysNumber($sysNumber)
    {
        $this->sysNumber = $sysNumber;
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
     * @param mixed $remarks
     */
    protected function setRemarks($remarks)
    {
        $this->remarks = $remarks;
    }

    /**
     *
     * @param mixed $group
     */
    protected function setGroup($group)
    {
        $this->group = $group;
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
    public function getAttributeCode()
    {
        return $this->attributeCode;
    }

    /**
     *
     * @return mixed
     */
    public function getAttributeName()
    {
        return $this->attributeName;
    }

    /**
     *
     * @return mixed
     */
    public function getAttributeName1()
    {
        return $this->attributeName1;
    }

    /**
     *
     * @return mixed
     */
    public function getAttributeName2()
    {
        return $this->attributeName2;
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
    public function getSysNumber()
    {
        return $this->sysNumber;
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
    public function getRemarks()
    {
        return $this->remarks;
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
}

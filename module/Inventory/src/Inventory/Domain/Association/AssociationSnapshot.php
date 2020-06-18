<?php
namespace Inventory\Domain\Association;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AssociationSnapshot
{

    public $id;

    public $uuid;

    public $isActive;

    public $hasBothDirection;

    public $createdOn;

    public $lastChangeOn;

    public $remarks;

    public $association;

    public $mainItem;

    public $relatedItem;

    public $createdBy;

    public $lastChangeBy;

    public $revisionNo;

    public $version;

    public $company;

    /**
     *
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
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
    public function getVersion()
    {
        return $this->version;
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
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     *
     * @return mixed
     */
    public function getHasBothDirection()
    {
        return $this->hasBothDirection;
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
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     *
     * @return mixed
     */
    public function getAssociation()
    {
        return $this->association;
    }

    /**
     *
     * @return mixed
     */
    public function getMainItem()
    {
        return $this->mainItem;
    }

    /**
     *
     * @return mixed
     */
    public function getRelatedItem()
    {
        return $this->relatedItem;
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
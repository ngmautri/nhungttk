<?php
namespace Application\Domain\Company\Department;

use Application\Domain\Shared\AbstractDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class DepartmentSnapshot extends AbstractDTO
{

    public $nodeId;

    public $nodeName;

    public $nodeParentId;

    public $path;

    public $pathDepth;

    public $status;

    public $remarks;

    public $createdOn;

    public $uuid;

    public $departmentName;

    public $departmentCode;

    public $isActive;

    public $lastChangeOn;

    public $lastChangeBy;

    public $departmentNameLocal;

    public $createdBy;

    public $company;

    /**
     *
     * @return mixed
     */
    protected function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     *
     * @param mixed $nodeId
     */
    protected function setNodeId($nodeId)
    {
        $this->nodeId = $nodeId;
    }

    /**
     *
     * @return mixed
     */
    protected function getNodeName()
    {
        return $this->nodeName;
    }

    /**
     *
     * @param mixed $nodeName
     */
    protected function setNodeName($nodeName)
    {
        $this->nodeName = $nodeName;
    }

    /**
     *
     * @return mixed
     */
    protected function getNodeParentId()
    {
        return $this->nodeParentId;
    }

    /**
     *
     * @param mixed $nodeParentId
     */
    protected function setNodeParentId($nodeParentId)
    {
        $this->nodeParentId = $nodeParentId;
    }

    /**
     *
     * @return mixed
     */
    protected function getPath()
    {
        return $this->path;
    }

    /**
     *
     * @param mixed $path
     */
    protected function setPath($path)
    {
        $this->path = $path;
    }

    /**
     *
     * @return mixed
     */
    protected function getPathDepth()
    {
        return $this->pathDepth;
    }

    /**
     *
     * @param mixed $pathDepth
     */
    protected function setPathDepth($pathDepth)
    {
        $this->pathDepth = $pathDepth;
    }

    /**
     *
     * @return mixed
     */
    protected function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @param mixed $status
     */
    protected function setStatus($status)
    {
        $this->status = $status;
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
    protected function getDepartmentName()
    {
        return $this->departmentName;
    }

    /**
     *
     * @param mixed $departmentName
     */
    protected function setDepartmentName($departmentName)
    {
        $this->departmentName = $departmentName;
    }

    /**
     *
     * @return mixed
     */
    protected function getDepartmentCode()
    {
        return $this->departmentCode;
    }

    /**
     *
     * @param mixed $departmentCode
     */
    protected function setDepartmentCode($departmentCode)
    {
        $this->departmentCode = $departmentCode;
    }

    /**
     *
     * @return mixed
     */
    protected function getIsActive()
    {
        return $this->isActive;
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
    protected function getDepartmentNameLocal()
    {
        return $this->departmentNameLocal;
    }

    /**
     *
     * @param mixed $departmentNameLocal
     */
    protected function setDepartmentNameLocal($departmentNameLocal)
    {
        $this->departmentNameLocal = $departmentNameLocal;
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
    protected function getCompany()
    {
        return $this->company;
    }

    /**
     *
     * @param mixed $company
     */
    protected function setCompany($company)
    {
        $this->company = $company;
    }
}
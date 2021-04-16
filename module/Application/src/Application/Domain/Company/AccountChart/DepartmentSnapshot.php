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

    public $parentName;

    public $parentCode;

    public $token;

    public $createdBy;

    public $company;

    /**
     *
     * @return mixed
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     *
     * @param mixed $nodeId
     */
    public function setNodeId($nodeId)
    {
        $this->nodeId = $nodeId;
    }

    /**
     *
     * @return mixed
     */
    public function getNodeName()
    {
        return $this->nodeName;
    }

    /**
     *
     * @param mixed $nodeName
     */
    public function setNodeName($nodeName)
    {
        $this->nodeName = $nodeName;
    }

    /**
     *
     * @return mixed
     */
    public function getNodeParentId()
    {
        return $this->nodeParentId;
    }

    /**
     *
     * @param mixed $nodeParentId
     */
    public function setNodeParentId($nodeParentId)
    {
        $this->nodeParentId = $nodeParentId;
    }

    /**
     *
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     *
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     *
     * @return mixed
     */
    public function getPathDepth()
    {
        return $this->pathDepth;
    }

    /**
     *
     * @param mixed $pathDepth
     */
    public function setPathDepth($pathDepth)
    {
        $this->pathDepth = $pathDepth;
    }

    /**
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
    public function getDepartmentName()
    {
        return $this->departmentName;
    }

    /**
     *
     * @param mixed $departmentName
     */
    public function setDepartmentName($departmentName)
    {
        $this->departmentName = $departmentName;
    }

    /**
     *
     * @return mixed
     */
    public function getDepartmentCode()
    {
        return $this->departmentCode;
    }

    /**
     *
     * @param mixed $departmentCode
     */
    public function setDepartmentCode($departmentCode)
    {
        $this->departmentCode = $departmentCode;
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
    public function getDepartmentNameLocal()
    {
        return $this->departmentNameLocal;
    }

    /**
     *
     * @param mixed $departmentNameLocal
     */
    public function setDepartmentNameLocal($departmentNameLocal)
    {
        $this->departmentNameLocal = $departmentNameLocal;
    }

    /**
     *
     * @return mixed
     */
    public function getParentName()
    {
        return $this->parentName;
    }

    /**
     *
     * @param mixed $parentName
     */
    public function setParentName($parentName)
    {
        $this->parentName = $parentName;
    }

    /**
     *
     * @return mixed
     */
    public function getParentCode()
    {
        return $this->parentCode;
    }

    /**
     *
     * @param mixed $parentCode
     */
    public function setParentCode($parentCode)
    {
        $this->parentCode = $parentCode;
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
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
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
    public function getCompany()
    {
        return $this->company;
    }

    /**
     *
     * @param mixed $company
     */
    public function setCompany($company)
    {
        $this->company = $company;
    }
}
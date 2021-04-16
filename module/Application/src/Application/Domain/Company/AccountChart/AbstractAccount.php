<?php
namespace Application\Domain\Company\Department;

use Application\Domain\Shared\AbstractEntity;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AbstractAccount extends AbstractEntity
{

    protected $nodeId;

    protected $nodeName;

    protected $nodeParentId;

    protected $path;

    protected $pathDepth;

    protected $status;

    protected $remarks;

    protected $createdOn;

    protected $uuid;

    protected $departmentName;

    protected $departmentCode;

    protected $isActive;

    protected $lastChangeOn;

    protected $lastChangeBy;

    protected $departmentNameLocal;

    protected $createdBy;

    protected $company;

    protected $parentName;

    protected $parentCode;

    protected $token;

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
     * @return mixed
     */
    public function getNodeName()
    {
        return $this->nodeName;
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
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
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
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
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
    public function getUuid()
    {
        return $this->uuid;
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
     * @return mixed
     */
    public function getDepartmentCode()
    {
        return $this->departmentCode;
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
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
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
    public function getDepartmentNameLocal()
    {
        return $this->departmentNameLocal;
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
    public function getCompany()
    {
        return $this->company;
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
     * @param mixed $nodeName
     */
    protected function setNodeName($nodeName)
    {
        $this->nodeName = $nodeName;
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
     * @param mixed $path
     */
    protected function setPath($path)
    {
        $this->path = $path;
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
     * @param mixed $status
     */
    protected function setStatus($status)
    {
        $this->status = $status;
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
     * @param mixed $createdOn
     */
    protected function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
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
     * @param mixed $departmentName
     */
    protected function setDepartmentName($departmentName)
    {
        $this->departmentName = $departmentName;
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
     * @param mixed $isActive
     */
    protected function setIsActive($isActive)
    {
        $this->isActive = $isActive;
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
     * @param mixed $lastChangeBy
     */
    protected function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
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
     * @param mixed $createdBy
     */
    protected function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @param mixed $company
     */
    protected function setCompany($company)
    {
        $this->company = $company;
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
     * @return mixed
     */
    public function getParentCode()
    {
        return $this->parentCode;
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
     * @param mixed $parentName
     */
    protected function setParentName($parentName)
    {
        $this->parentName = $parentName;
    }

    /**
     *
     * @param mixed $parentCode
     */
    protected function setParentCode($parentCode)
    {
        $this->parentCode = $parentCode;
    }

    /**
     *
     * @param mixed $token
     */
    protected function setToken($token)
    {
        $this->token = $token;
    }
}

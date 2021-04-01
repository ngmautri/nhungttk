<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationDepartment
 *
 * @ORM\Table(name="nmt_application_department", indexes={@ORM\Index(name="nmt_application_department_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_application_department_FK2_idx", columns={"company_id"})})
 * @ORM\Entity
 */
class NmtApplicationDepartment
{
    /**
     * @var integer
     *
     * @ORM\Column(name="node_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $nodeId;

    /**
     * @var string
     *
     * @ORM\Column(name="node_name", type="string", length=100, nullable=false)
     */
    private $nodeName;

    /**
     * @var integer
     *
     * @ORM\Column(name="node_parent_id", type="integer", nullable=true)
     */
    private $nodeParentId;

    /**
     * @var string
     *
     * @ORM\Column(name="path", type="string", length=255, nullable=true)
     */
    private $path;

    /**
     * @var integer
     *
     * @ORM\Column(name="path_depth", type="integer", nullable=true)
     */
    private $pathDepth;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=45, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="text", length=65535, nullable=true)
     */
    private $remarks;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn = 'CURRENT_TIMESTAMP';

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, nullable=true)
     */
    private $uuid;

    /**
     * @var string
     *
     * @ORM\Column(name="department_name", type="string", length=100, nullable=true)
     */
    private $departmentName;

    /**
     * @var string
     *
     * @ORM\Column(name="department_code", type="string", length=45, nullable=true)
     */
    private $departmentCode;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_change_on", type="datetime", nullable=true)
     */
    private $lastChangeOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="last_change_by", type="integer", nullable=true)
     */
    private $lastChangeBy;

    /**
     * @var string
     *
     * @ORM\Column(name="department_name_local", type="string", length=100, nullable=true)
     */
    private $departmentNameLocal;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="created_by", referencedColumnName="id")
     * })
     */
    private $createdBy;

    /**
     * @var \Application\Entity\NmtApplicationCompany
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCompany")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;



    /**
     * Get nodeId
     *
     * @return integer
     */
    public function getNodeId()
    {
        return $this->nodeId;
    }

    /**
     * Set nodeName
     *
     * @param string $nodeName
     *
     * @return NmtApplicationDepartment
     */
    public function setNodeName($nodeName)
    {
        $this->nodeName = $nodeName;

        return $this;
    }

    /**
     * Get nodeName
     *
     * @return string
     */
    public function getNodeName()
    {
        return $this->nodeName;
    }

    /**
     * Set nodeParentId
     *
     * @param integer $nodeParentId
     *
     * @return NmtApplicationDepartment
     */
    public function setNodeParentId($nodeParentId)
    {
        $this->nodeParentId = $nodeParentId;

        return $this;
    }

    /**
     * Get nodeParentId
     *
     * @return integer
     */
    public function getNodeParentId()
    {
        return $this->nodeParentId;
    }

    /**
     * Set path
     *
     * @param string $path
     *
     * @return NmtApplicationDepartment
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * Get path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * Set pathDepth
     *
     * @param integer $pathDepth
     *
     * @return NmtApplicationDepartment
     */
    public function setPathDepth($pathDepth)
    {
        $this->pathDepth = $pathDepth;

        return $this;
    }

    /**
     * Get pathDepth
     *
     * @return integer
     */
    public function getPathDepth()
    {
        return $this->pathDepth;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return NmtApplicationDepartment
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtApplicationDepartment
     */
    public function setRemarks($remarks)
    {
        $this->remarks = $remarks;

        return $this;
    }

    /**
     * Get remarks
     *
     * @return string
     */
    public function getRemarks()
    {
        return $this->remarks;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtApplicationDepartment
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;

        return $this;
    }

    /**
     * Get createdOn
     *
     * @return \DateTime
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * Set uuid
     *
     * @param string $uuid
     *
     * @return NmtApplicationDepartment
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * Set departmentName
     *
     * @param string $departmentName
     *
     * @return NmtApplicationDepartment
     */
    public function setDepartmentName($departmentName)
    {
        $this->departmentName = $departmentName;

        return $this;
    }

    /**
     * Get departmentName
     *
     * @return string
     */
    public function getDepartmentName()
    {
        return $this->departmentName;
    }

    /**
     * Set departmentCode
     *
     * @param string $departmentCode
     *
     * @return NmtApplicationDepartment
     */
    public function setDepartmentCode($departmentCode)
    {
        $this->departmentCode = $departmentCode;

        return $this;
    }

    /**
     * Get departmentCode
     *
     * @return string
     */
    public function getDepartmentCode()
    {
        return $this->departmentCode;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtApplicationDepartment
     */
    public function setIsActive($isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get isActive
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set lastChangeOn
     *
     * @param \DateTime $lastChangeOn
     *
     * @return NmtApplicationDepartment
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;

        return $this;
    }

    /**
     * Get lastChangeOn
     *
     * @return \DateTime
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     * Set lastChangeBy
     *
     * @param integer $lastChangeBy
     *
     * @return NmtApplicationDepartment
     */
    public function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;

        return $this;
    }

    /**
     * Get lastChangeBy
     *
     * @return integer
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    /**
     * Set departmentNameLocal
     *
     * @param string $departmentNameLocal
     *
     * @return NmtApplicationDepartment
     */
    public function setDepartmentNameLocal($departmentNameLocal)
    {
        $this->departmentNameLocal = $departmentNameLocal;

        return $this;
    }

    /**
     * Get departmentNameLocal
     *
     * @return string
     */
    public function getDepartmentNameLocal()
    {
        return $this->departmentNameLocal;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtApplicationDepartment
     */
    public function setCreatedBy(\Application\Entity\MlaUsers $createdBy = null)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set company
     *
     * @param \Application\Entity\NmtApplicationCompany $company
     *
     * @return NmtApplicationDepartment
     */
    public function setCompany(\Application\Entity\NmtApplicationCompany $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Application\Entity\NmtApplicationCompany
     */
    public function getCompany()
    {
        return $this->company;
    }
}

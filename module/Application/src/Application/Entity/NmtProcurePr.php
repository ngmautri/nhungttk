<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtProcurePr
 *
 * @ORM\Table(name="nmt_procure_pr", indexes={@ORM\Index(name="nmt_procure_pr_KF1_idx", columns={"created_by"}), @ORM\Index(name="nmt_procure_pr_KF2_idx", columns={"last_change_by"}), @ORM\Index(name="nmt_procure_pr_KF3_idx", columns={"department_id"})})
 * @ORM\Entity
 */
class NmtProcurePr
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="pr_auto_number", type="string", length=45, nullable=true)
     */
    private $prAutoNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="pr_number", type="string", length=45, nullable=false)
     */
    private $prNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="pr_name", type="string", length=45, nullable=false)
     */
    private $prName;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="string", length=100, nullable=true)
     */
    private $keywords;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_change_on", type="datetime", nullable=true)
     */
    private $lastChangeOn;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_draft", type="boolean", nullable=true)
     */
    private $isDraft;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=45, nullable=true)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="checksum", type="string", length=45, nullable=true)
     */
    private $checksum;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="submitted_on", type="datetime", nullable=true)
     */
    private $submittedOn;

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
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_change_by", referencedColumnName="id")
     * })
     */
    private $lastChangeBy;

    /**
     * @var \Application\Entity\NmtApplicationDepartment
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationDepartment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="department_id", referencedColumnName="node_id")
     * })
     */
    private $department;



    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set prAutoNumber
     *
     * @param string $prAutoNumber
     *
     * @return NmtProcurePr
     */
    public function setPrAutoNumber($prAutoNumber)
    {
        $this->prAutoNumber = $prAutoNumber;

        return $this;
    }

    /**
     * Get prAutoNumber
     *
     * @return string
     */
    public function getPrAutoNumber()
    {
        return $this->prAutoNumber;
    }

    /**
     * Set prNumber
     *
     * @param string $prNumber
     *
     * @return NmtProcurePr
     */
    public function setPrNumber($prNumber)
    {
        $this->prNumber = $prNumber;

        return $this;
    }

    /**
     * Get prNumber
     *
     * @return string
     */
    public function getPrNumber()
    {
        return $this->prNumber;
    }

    /**
     * Set prName
     *
     * @param string $prName
     *
     * @return NmtProcurePr
     */
    public function setPrName($prName)
    {
        $this->prName = $prName;

        return $this;
    }

    /**
     * Get prName
     *
     * @return string
     */
    public function getPrName()
    {
        return $this->prName;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     *
     * @return NmtProcurePr
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtProcurePr
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
     * @return NmtProcurePr
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
     * Set lastChangeOn
     *
     * @param \DateTime $lastChangeOn
     *
     * @return NmtProcurePr
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
     * Set isDraft
     *
     * @param boolean $isDraft
     *
     * @return NmtProcurePr
     */
    public function setIsDraft($isDraft)
    {
        $this->isDraft = $isDraft;

        return $this;
    }

    /**
     * Get isDraft
     *
     * @return boolean
     */
    public function getIsDraft()
    {
        return $this->isDraft;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtProcurePr
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
     * Set status
     *
     * @param string $status
     *
     * @return NmtProcurePr
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
     * Set token
     *
     * @param string $token
     *
     * @return NmtProcurePr
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set checksum
     *
     * @param string $checksum
     *
     * @return NmtProcurePr
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;

        return $this;
    }

    /**
     * Get checksum
     *
     * @return string
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * Set submittedOn
     *
     * @param \DateTime $submittedOn
     *
     * @return NmtProcurePr
     */
    public function setSubmittedOn($submittedOn)
    {
        $this->submittedOn = $submittedOn;

        return $this;
    }

    /**
     * Get submittedOn
     *
     * @return \DateTime
     */
    public function getSubmittedOn()
    {
        return $this->submittedOn;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtProcurePr
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
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return NmtProcurePr
     */
    public function setLastChangeBy(\Application\Entity\MlaUsers $lastChangeBy = null)
    {
        $this->lastChangeBy = $lastChangeBy;

        return $this;
    }

    /**
     * Get lastChangeBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    /**
     * Set department
     *
     * @param \Application\Entity\NmtApplicationDepartment $department
     *
     * @return NmtProcurePr
     */
    public function setDepartment(\Application\Entity\NmtApplicationDepartment $department = null)
    {
        $this->department = $department;

        return $this;
    }

    /**
     * Get department
     *
     * @return \Application\Entity\NmtApplicationDepartment
     */
    public function getDepartment()
    {
        return $this->department;
    }
}

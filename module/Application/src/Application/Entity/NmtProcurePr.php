<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtProcurePr
 *
 * @ORM\Table(name="nmt_procure_pr", indexes={@ORM\Index(name="nmt_procure_pr_FK1_idx", columns={"department_id"}), @ORM\Index(name="nmt_procure_pr_FK1_idx1", columns={"created_by"}), @ORM\Index(name="nmt_procure_pr_FK3_idx", columns={"last_changed_by"})})
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
     * @ORM\Column(name="pr_number", type="string", length=50, nullable=true)
     */
    private $prNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="pr_name", type="string", length=50, nullable=true)
     */
    private $prName;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=200, nullable=true)
     */
    private $description;

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
     * @var \Application\Entity\NmtApplicationDepartment
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationDepartment")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="department_id", referencedColumnName="node_id")
     * })
     */
    private $department;

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
     *   @ORM\JoinColumn(name="last_changed_by", referencedColumnName="id")
     * })
     */
    private $lastChangedBy;



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
     * Set description
     *
     * @param string $description
     *
     * @return NmtProcurePr
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
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
     * Set lastChangedBy
     *
     * @param \Application\Entity\MlaUsers $lastChangedBy
     *
     * @return NmtProcurePr
     */
    public function setLastChangedBy(\Application\Entity\MlaUsers $lastChangedBy = null)
    {
        $this->lastChangedBy = $lastChangedBy;

        return $this;
    }

    /**
     * Get lastChangedBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getLastChangedBy()
    {
        return $this->lastChangedBy;
    }
}

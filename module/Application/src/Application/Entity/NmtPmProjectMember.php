<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtPmProjectMember
 *
 * @ORM\Table(name="nmt_pm_project_member", indexes={@ORM\Index(name="nmt_pm_project_member_FK1_idx", columns={"project_id"}), @ORM\Index(name="nmt_pm_project_member_FK2_idx", columns={"user_id"}), @ORM\Index(name="nmt_pm_project_member_FK3_idx", columns={"created_by"}), @ORM\Index(name="nmt_pm_project_member_FK4_idx", columns={"last_change_by"})})
 * @ORM\Entity
 */
class NmtPmProjectMember
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
     * @ORM\Column(name="status", type="string", length=45, nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="assigned_on", type="datetime", nullable=true)
     */
    private $assignedOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="unassigned_on", type="datetime", nullable=true)
     */
    private $unassignedOn;

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
     * @var \Application\Entity\NmtPmProject
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtPmProject")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="project_id", referencedColumnName="id")
     * })
     */
    private $project;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return NmtPmProjectMember
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
     * Set assignedOn
     *
     * @param \DateTime $assignedOn
     *
     * @return NmtPmProjectMember
     */
    public function setAssignedOn($assignedOn)
    {
        $this->assignedOn = $assignedOn;

        return $this;
    }

    /**
     * Get assignedOn
     *
     * @return \DateTime
     */
    public function getAssignedOn()
    {
        return $this->assignedOn;
    }

    /**
     * Set unassignedOn
     *
     * @param \DateTime $unassignedOn
     *
     * @return NmtPmProjectMember
     */
    public function setUnassignedOn($unassignedOn)
    {
        $this->unassignedOn = $unassignedOn;

        return $this;
    }

    /**
     * Get unassignedOn
     *
     * @return \DateTime
     */
    public function getUnassignedOn()
    {
        return $this->unassignedOn;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtPmProjectMember
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
     * @return NmtPmProjectMember
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
     * Set project
     *
     * @param \Application\Entity\NmtPmProject $project
     *
     * @return NmtPmProjectMember
     */
    public function setProject(\Application\Entity\NmtPmProject $project = null)
    {
        $this->project = $project;

        return $this;
    }

    /**
     * Get project
     *
     * @return \Application\Entity\NmtPmProject
     */
    public function getProject()
    {
        return $this->project;
    }

    /**
     * Set user
     *
     * @param \Application\Entity\MlaUsers $user
     *
     * @return NmtPmProjectMember
     */
    public function setUser(\Application\Entity\MlaUsers $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtPmProjectMember
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
     * @return NmtPmProjectMember
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
}

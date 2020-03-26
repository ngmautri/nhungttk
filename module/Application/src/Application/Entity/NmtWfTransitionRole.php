<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtWfTransitionRole
 *
 * @ORM\Table(name="nmt_wf_transition_role", indexes={@ORM\Index(name="nmt_wf_transition_role_FK1_idx", columns={"transition_id"}), @ORM\Index(name="nmt_wf_transition_role_FK2_idx", columns={"role_id"}), @ORM\Index(name="nmt_wf_transition_role_FK3_idx", columns={"created_by"})})
 * @ORM\Entity
 */
class NmtWfTransitionRole
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
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \Application\Entity\NmtWfTransition
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfTransition")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="transition_id", referencedColumnName="id")
     * })
     */
    private $transition;

    /**
     * @var \Application\Entity\NmtApplicationAclRole
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationAclRole")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="role_id", referencedColumnName="id")
     * })
     */
    private $role;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtWfTransitionRole
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
     * Set transition
     *
     * @param \Application\Entity\NmtWfTransition $transition
     *
     * @return NmtWfTransitionRole
     */
    public function setTransition(\Application\Entity\NmtWfTransition $transition = null)
    {
        $this->transition = $transition;

        return $this;
    }

    /**
     * Get transition
     *
     * @return \Application\Entity\NmtWfTransition
     */
    public function getTransition()
    {
        return $this->transition;
    }

    /**
     * Set role
     *
     * @param \Application\Entity\NmtApplicationAclRole $role
     *
     * @return NmtWfTransitionRole
     */
    public function setRole(\Application\Entity\NmtApplicationAclRole $role = null)
    {
        $this->role = $role;

        return $this;
    }

    /**
     * Get role
     *
     * @return \Application\Entity\NmtApplicationAclRole
     */
    public function getRole()
    {
        return $this->role;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtWfTransitionRole
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
}

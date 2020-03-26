<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtWfTransition
 *
 * @ORM\Table(name="nmt_wf_transition", indexes={@ORM\Index(name="nmt_wf_transition_FK1_idx", columns={"agent_id"}), @ORM\Index(name="nmt_wf_transition_FK2_idx", columns={"role_id"}), @ORM\Index(name="nmt_wf_transition_FK3_idx", columns={"created_by"}), @ORM\Index(name="nmt_wf_transition_FK4_idx", columns={"workflow_id"})})
 * @ORM\Entity
 */
class NmtWfTransition
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
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_name", type="string", length=45, nullable=true)
     */
    private $workflowName;

    /**
     * @var string
     *
     * @ORM\Column(name="transition_name", type="string", length=45, nullable=true)
     */
    private $transitionName;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=200, nullable=true)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="froms", type="string", length=45, nullable=true)
     */
    private $froms;

    /**
     * @var string
     *
     * @ORM\Column(name="tos", type="string", length=45, nullable=true)
     */
    private $tos;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agent_id", referencedColumnName="id")
     * })
     */
    private $agent;

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
     * @var \Application\Entity\NmtWfWorkflow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfWorkflow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="workflow_id", referencedColumnName="id")
     * })
     */
    private $workflow;



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
     * Set token
     *
     * @param string $token
     *
     * @return NmtWfTransition
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
     * Set workflowName
     *
     * @param string $workflowName
     *
     * @return NmtWfTransition
     */
    public function setWorkflowName($workflowName)
    {
        $this->workflowName = $workflowName;

        return $this;
    }

    /**
     * Get workflowName
     *
     * @return string
     */
    public function getWorkflowName()
    {
        return $this->workflowName;
    }

    /**
     * Set transitionName
     *
     * @param string $transitionName
     *
     * @return NmtWfTransition
     */
    public function setTransitionName($transitionName)
    {
        $this->transitionName = $transitionName;

        return $this;
    }

    /**
     * Get transitionName
     *
     * @return string
     */
    public function getTransitionName()
    {
        return $this->transitionName;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtWfTransition
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtWfTransition
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtWfTransition
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
     * Set froms
     *
     * @param string $froms
     *
     * @return NmtWfTransition
     */
    public function setFroms($froms)
    {
        $this->froms = $froms;

        return $this;
    }

    /**
     * Get froms
     *
     * @return string
     */
    public function getFroms()
    {
        return $this->froms;
    }

    /**
     * Set tos
     *
     * @param string $tos
     *
     * @return NmtWfTransition
     */
    public function setTos($tos)
    {
        $this->tos = $tos;

        return $this;
    }

    /**
     * Get tos
     *
     * @return string
     */
    public function getTos()
    {
        return $this->tos;
    }

    /**
     * Set agent
     *
     * @param \Application\Entity\MlaUsers $agent
     *
     * @return NmtWfTransition
     */
    public function setAgent(\Application\Entity\MlaUsers $agent = null)
    {
        $this->agent = $agent;

        return $this;
    }

    /**
     * Get agent
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * Set role
     *
     * @param \Application\Entity\NmtApplicationAclRole $role
     *
     * @return NmtWfTransition
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
     * @return NmtWfTransition
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
     * Set workflow
     *
     * @param \Application\Entity\NmtWfWorkflow $workflow
     *
     * @return NmtWfTransition
     */
    public function setWorkflow(\Application\Entity\NmtWfWorkflow $workflow = null)
    {
        $this->workflow = $workflow;

        return $this;
    }

    /**
     * Get workflow
     *
     * @return \Application\Entity\NmtWfWorkflow
     */
    public function getWorkflow()
    {
        return $this->workflow;
    }
}

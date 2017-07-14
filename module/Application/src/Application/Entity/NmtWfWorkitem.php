<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtWfWorkitem
 *
 * @ORM\Table(name="nmt_wf_workitem", indexes={@ORM\Index(name="nmt_wf_workitem_idx", columns={"agent_id"}), @ORM\Index(name="nmt_wf_workitem_FK2_idx", columns={"agent_role_id"})})
 * @ORM\Entity
 */
class NmtWfWorkitem
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
     * @var integer
     *
     * @ORM\Column(name="workflow_id", type="integer", nullable=true)
     */
    private $workflowId;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_name", type="string", length=45, nullable=false)
     */
    private $workflowName;

    /**
     * @var integer
     *
     * @ORM\Column(name="transition_id", type="integer", nullable=true)
     */
    private $transitionId;

    /**
     * @var string
     *
     * @ORM\Column(name="transition_name", type="string", length=45, nullable=false)
     */
    private $transitionName;

    /**
     * @var string
     *
     * @ORM\Column(name="workitem_status", type="string", length=45, nullable=true)
     */
    private $workitemStatus;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="deadline", type="datetime", nullable=true)
     */
    private $deadline;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="finished_date", type="datetime", nullable=true)
     */
    private $finishedDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="enabled_date", type="datetime", nullable=true)
     */
    private $enabledDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="cancelled_date", type="datetime", nullable=true)
     */
    private $cancelledDate;

    /**
     * @var integer
     *
     * @ORM\Column(name="subject_id", type="integer", nullable=false)
     */
    private $subjectId;

    /**
     * @var string
     *
     * @ORM\Column(name="subject_class", type="string", length=80, nullable=false)
     */
    private $subjectClass;

    /**
     * @var string
     *
     * @ORM\Column(name="remark", type="string", length=100, nullable=true)
     */
    private $remark;

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
     *   @ORM\JoinColumn(name="agent_role_id", referencedColumnName="id")
     * })
     */
    private $agentRole;



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
     * @return NmtWfWorkitem
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
     * Set workflowId
     *
     * @param integer $workflowId
     *
     * @return NmtWfWorkitem
     */
    public function setWorkflowId($workflowId)
    {
        $this->workflowId = $workflowId;

        return $this;
    }

    /**
     * Get workflowId
     *
     * @return integer
     */
    public function getWorkflowId()
    {
        return $this->workflowId;
    }

    /**
     * Set workflowName
     *
     * @param string $workflowName
     *
     * @return NmtWfWorkitem
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
     * Set transitionId
     *
     * @param integer $transitionId
     *
     * @return NmtWfWorkitem
     */
    public function setTransitionId($transitionId)
    {
        $this->transitionId = $transitionId;

        return $this;
    }

    /**
     * Get transitionId
     *
     * @return integer
     */
    public function getTransitionId()
    {
        return $this->transitionId;
    }

    /**
     * Set transitionName
     *
     * @param string $transitionName
     *
     * @return NmtWfWorkitem
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
     * Set workitemStatus
     *
     * @param string $workitemStatus
     *
     * @return NmtWfWorkitem
     */
    public function setWorkitemStatus($workitemStatus)
    {
        $this->workitemStatus = $workitemStatus;

        return $this;
    }

    /**
     * Get workitemStatus
     *
     * @return string
     */
    public function getWorkitemStatus()
    {
        return $this->workitemStatus;
    }

    /**
     * Set deadline
     *
     * @param \DateTime $deadline
     *
     * @return NmtWfWorkitem
     */
    public function setDeadline($deadline)
    {
        $this->deadline = $deadline;

        return $this;
    }

    /**
     * Get deadline
     *
     * @return \DateTime
     */
    public function getDeadline()
    {
        return $this->deadline;
    }

    /**
     * Set finishedDate
     *
     * @param \DateTime $finishedDate
     *
     * @return NmtWfWorkitem
     */
    public function setFinishedDate($finishedDate)
    {
        $this->finishedDate = $finishedDate;

        return $this;
    }

    /**
     * Get finishedDate
     *
     * @return \DateTime
     */
    public function getFinishedDate()
    {
        return $this->finishedDate;
    }

    /**
     * Set enabledDate
     *
     * @param \DateTime $enabledDate
     *
     * @return NmtWfWorkitem
     */
    public function setEnabledDate($enabledDate)
    {
        $this->enabledDate = $enabledDate;

        return $this;
    }

    /**
     * Get enabledDate
     *
     * @return \DateTime
     */
    public function getEnabledDate()
    {
        return $this->enabledDate;
    }

    /**
     * Set cancelledDate
     *
     * @param \DateTime $cancelledDate
     *
     * @return NmtWfWorkitem
     */
    public function setCancelledDate($cancelledDate)
    {
        $this->cancelledDate = $cancelledDate;

        return $this;
    }

    /**
     * Get cancelledDate
     *
     * @return \DateTime
     */
    public function getCancelledDate()
    {
        return $this->cancelledDate;
    }

    /**
     * Set subjectId
     *
     * @param integer $subjectId
     *
     * @return NmtWfWorkitem
     */
    public function setSubjectId($subjectId)
    {
        $this->subjectId = $subjectId;

        return $this;
    }

    /**
     * Get subjectId
     *
     * @return integer
     */
    public function getSubjectId()
    {
        return $this->subjectId;
    }

    /**
     * Set subjectClass
     *
     * @param string $subjectClass
     *
     * @return NmtWfWorkitem
     */
    public function setSubjectClass($subjectClass)
    {
        $this->subjectClass = $subjectClass;

        return $this;
    }

    /**
     * Get subjectClass
     *
     * @return string
     */
    public function getSubjectClass()
    {
        return $this->subjectClass;
    }

    /**
     * Set remark
     *
     * @param string $remark
     *
     * @return NmtWfWorkitem
     */
    public function setRemark($remark)
    {
        $this->remark = $remark;

        return $this;
    }

    /**
     * Get remark
     *
     * @return string
     */
    public function getRemark()
    {
        return $this->remark;
    }

    /**
     * Set agent
     *
     * @param \Application\Entity\MlaUsers $agent
     *
     * @return NmtWfWorkitem
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
     * Set agentRole
     *
     * @param \Application\Entity\NmtApplicationAclRole $agentRole
     *
     * @return NmtWfWorkitem
     */
    public function setAgentRole(\Application\Entity\NmtApplicationAclRole $agentRole = null)
    {
        $this->agentRole = $agentRole;

        return $this;
    }

    /**
     * Get agentRole
     *
     * @return \Application\Entity\NmtApplicationAclRole
     */
    public function getAgentRole()
    {
        return $this->agentRole;
    }
}

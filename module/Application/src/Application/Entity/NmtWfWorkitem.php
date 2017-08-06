<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtWfWorkitem
 *
 * @ORM\Table(name="nmt_wf_workitem", indexes={@ORM\Index(name="nmt_wf_workitem_FK2_idx", columns={"transition_id"}), @ORM\Index(name="nmt_wf_workitem_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_wf_workitem_FK3_idx", columns={"workflow_id"}), @ORM\Index(name="nmt_wf_workitem_FK4_idx", columns={"agent_id"})})
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
     * @var string
     *
     * @ORM\Column(name="workflow_name", type="string", length=45, nullable=false)
     */
    private $workflowName;

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
     * @ORM\Column(name="agent_role_id", type="integer", nullable=true)
     */
    private $agentRoleId;

    /**
     * @var integer
     *
     * @ORM\Column(name="subject_id", type="integer", nullable=false)
     */
    private $subjectId;

    /**
     * @var string
     *
     * @ORM\Column(name="subject_token", type="string", length=45, nullable=true)
     */
    private $subjectToken;

    /**
     * @var string
     *
     * @ORM\Column(name="subject_class", type="string", length=80, nullable=false)
     */
    private $subjectClass;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=100, nullable=true)
     */
    private $remarks;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="subject_handler", type="string", length=100, nullable=true)
     */
    private $subjectHandler;

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
     * @var \Application\Entity\NmtWfTransition
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfTransition")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="transition_id", referencedColumnName="id")
     * })
     */
    private $transition;

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
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="agent_id", referencedColumnName="id")
     * })
     */
    private $agent;



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
     * Set agentRoleId
     *
     * @param integer $agentRoleId
     *
     * @return NmtWfWorkitem
     */
    public function setAgentRoleId($agentRoleId)
    {
        $this->agentRoleId = $agentRoleId;

        return $this;
    }

    /**
     * Get agentRoleId
     *
     * @return integer
     */
    public function getAgentRoleId()
    {
        return $this->agentRoleId;
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
     * Set subjectToken
     *
     * @param string $subjectToken
     *
     * @return NmtWfWorkitem
     */
    public function setSubjectToken($subjectToken)
    {
        $this->subjectToken = $subjectToken;

        return $this;
    }

    /**
     * Get subjectToken
     *
     * @return string
     */
    public function getSubjectToken()
    {
        return $this->subjectToken;
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return NmtWfWorkitem
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
     * @return NmtWfWorkitem
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
     * Set subjectHandler
     *
     * @param string $subjectHandler
     *
     * @return NmtWfWorkitem
     */
    public function setSubjectHandler($subjectHandler)
    {
        $this->subjectHandler = $subjectHandler;

        return $this;
    }

    /**
     * Get subjectHandler
     *
     * @return string
     */
    public function getSubjectHandler()
    {
        return $this->subjectHandler;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtWfWorkitem
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
     * Set transition
     *
     * @param \Application\Entity\NmtWfTransition $transition
     *
     * @return NmtWfWorkitem
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
     * Set workflow
     *
     * @param \Application\Entity\NmtWfWorkflow $workflow
     *
     * @return NmtWfWorkitem
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
}

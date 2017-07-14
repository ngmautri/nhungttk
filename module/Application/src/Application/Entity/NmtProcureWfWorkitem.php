<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtProcureWfWorkitem
 *
 * @ORM\Table(name="nmt_procure_wf_workitem", indexes={@ORM\Index(name="nmt_procure_wf_workitem_idx", columns={"agent_id"}), @ORM\Index(name="nmt_procure_wf_workitem_FK2_idx", columns={"agent_role_id"}), @ORM\Index(name="nmt_procure_wf_workitem_FK3_idx", columns={"pr_id"}), @ORM\Index(name="nmt_procure_wf_workitem_FK4_idx", columns={"pr_row_id"})})
 * @ORM\Entity
 */
class NmtProcureWfWorkitem
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
     * @var \Application\Entity\NmtProcurePr
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcurePr")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pr_id", referencedColumnName="id")
     * })
     */
    private $pr;

    /**
     * @var \Application\Entity\NmtProcurePrRow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtProcurePrRow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="pr_row_id", referencedColumnName="id")
     * })
     */
    private $prRow;



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
     * @return NmtProcureWfWorkitem
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
     * @return NmtProcureWfWorkitem
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
     * @return NmtProcureWfWorkitem
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
     * @return NmtProcureWfWorkitem
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
     * @return NmtProcureWfWorkitem
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
     * @return NmtProcureWfWorkitem
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
     * @return NmtProcureWfWorkitem
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
     * @return NmtProcureWfWorkitem
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
     * @return NmtProcureWfWorkitem
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
     * @return NmtProcureWfWorkitem
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
     * @return NmtProcureWfWorkitem
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
     * @return NmtProcureWfWorkitem
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
     * @return NmtProcureWfWorkitem
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
     * @return NmtProcureWfWorkitem
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
     * @return NmtProcureWfWorkitem
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

    /**
     * Set pr
     *
     * @param \Application\Entity\NmtProcurePr $pr
     *
     * @return NmtProcureWfWorkitem
     */
    public function setPr(\Application\Entity\NmtProcurePr $pr = null)
    {
        $this->pr = $pr;

        return $this;
    }

    /**
     * Get pr
     *
     * @return \Application\Entity\NmtProcurePr
     */
    public function getPr()
    {
        return $this->pr;
    }

    /**
     * Set prRow
     *
     * @param \Application\Entity\NmtProcurePrRow $prRow
     *
     * @return NmtProcureWfWorkitem
     */
    public function setPrRow(\Application\Entity\NmtProcurePrRow $prRow = null)
    {
        $this->prRow = $prRow;

        return $this;
    }

    /**
     * Get prRow
     *
     * @return \Application\Entity\NmtProcurePrRow
     */
    public function getPrRow()
    {
        return $this->prRow;
    }
}

<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtInventoryWfWorkitem
 *
 * @ORM\Table(name="nmt_inventory_wf_workitem")
 * @ORM\Entity
 */
class NmtInventoryWfWorkitem
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
     * @ORM\Column(name="agent_id", type="integer", nullable=true)
     */
    private $agentId;

    /**
     * @var integer
     *
     * @ORM\Column(name="agent_role_id", type="integer", nullable=true)
     */
    private $agentRoleId;

    /**
     * @var integer
     *
     * @ORM\Column(name="item_id", type="integer", nullable=true)
     */
    private $itemId;



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
     * @return NmtInventoryWfWorkitem
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
     * @return NmtInventoryWfWorkitem
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
     * @return NmtInventoryWfWorkitem
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
     * @return NmtInventoryWfWorkitem
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
     * @return NmtInventoryWfWorkitem
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
     * @return NmtInventoryWfWorkitem
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
     * @return NmtInventoryWfWorkitem
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
     * @return NmtInventoryWfWorkitem
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
     * @return NmtInventoryWfWorkitem
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
     * @return NmtInventoryWfWorkitem
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
     * Set agentId
     *
     * @param integer $agentId
     *
     * @return NmtInventoryWfWorkitem
     */
    public function setAgentId($agentId)
    {
        $this->agentId = $agentId;

        return $this;
    }

    /**
     * Get agentId
     *
     * @return integer
     */
    public function getAgentId()
    {
        return $this->agentId;
    }

    /**
     * Set agentRoleId
     *
     * @param integer $agentRoleId
     *
     * @return NmtInventoryWfWorkitem
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
     * Set itemId
     *
     * @param integer $itemId
     *
     * @return NmtInventoryWfWorkitem
     */
    public function setItemId($itemId)
    {
        $this->itemId = $itemId;

        return $this;
    }

    /**
     * Get itemId
     *
     * @return integer
     */
    public function getItemId()
    {
        return $this->itemId;
    }
}

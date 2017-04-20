<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtWfWorkitem
 *
 * @ORM\Table(name="nmt_wf_workitem", indexes={@ORM\Index(name="nmt_wf_workitem_FK1_idx", columns={"case_id"}), @ORM\Index(name="nmt_wf_workitem_FK2_idx", columns={"workflow_id"}), @ORM\Index(name="nmt_wf_workitem_FK3_idx", columns={"transition_id"}), @ORM\Index(name="nmt_wf_workitem_FK4_idx", columns={"workitem_use_id"}), @ORM\Index(name="nmt_wf_workitem_FK5_idx", columns={"node_id"})})
 * @ORM\Entity
 */
class NmtWfWorkitem
{
    /**
     * @var integer
     *
     * @ORM\Column(name="workitem_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $workitemId;

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
     * @var \DateTime
     *
     * @ORM\Column(name="finished_date", type="datetime", nullable=true)
     */
    private $finishedDate;

    /**
     * @var \Application\Entity\NmtWfCase
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfCase")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="case_id", referencedColumnName="case_id")
     * })
     */
    private $case;

    /**
     * @var \Application\Entity\NmtWfWorkflow
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfWorkflow")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="workflow_id", referencedColumnName="workflow_id")
     * })
     */
    private $workflow;

    /**
     * @var \Application\Entity\NmtWfTransition
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfTransition")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="transition_id", referencedColumnName="transition_id")
     * })
     */
    private $transition;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="workitem_use_id", referencedColumnName="id")
     * })
     */
    private $workitemUse;

    /**
     * @var \Application\Entity\NmtWfNode
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtWfNode")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="node_id", referencedColumnName="node_id")
     * })
     */
    private $node;



    /**
     * Get workitemId
     *
     * @return integer
     */
    public function getWorkitemId()
    {
        return $this->workitemId;
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
     * Set case
     *
     * @param \Application\Entity\NmtWfCase $case
     *
     * @return NmtWfWorkitem
     */
    public function setCase(\Application\Entity\NmtWfCase $case = null)
    {
        $this->case = $case;

        return $this;
    }

    /**
     * Get case
     *
     * @return \Application\Entity\NmtWfCase
     */
    public function getCase()
    {
        return $this->case;
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
     * Set workitemUse
     *
     * @param \Application\Entity\MlaUsers $workitemUse
     *
     * @return NmtWfWorkitem
     */
    public function setWorkitemUse(\Application\Entity\MlaUsers $workitemUse = null)
    {
        $this->workitemUse = $workitemUse;

        return $this;
    }

    /**
     * Get workitemUse
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getWorkitemUse()
    {
        return $this->workitemUse;
    }

    /**
     * Set node
     *
     * @param \Application\Entity\NmtWfNode $node
     *
     * @return NmtWfWorkitem
     */
    public function setNode(\Application\Entity\NmtWfNode $node = null)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * Get node
     *
     * @return \Application\Entity\NmtWfNode
     */
    public function getNode()
    {
        return $this->node;
    }
}

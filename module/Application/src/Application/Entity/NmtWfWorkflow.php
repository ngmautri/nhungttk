<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtWfWorkflow
 *
 * @ORM\Table(name="nmt_wf_workflow", indexes={@ORM\Index(name="nmt_wf_workflow_idx", columns={"workflow_created_by"})})
 * @ORM\Entity
 */
class NmtWfWorkflow
{
    /**
     * @var integer
     *
     * @ORM\Column(name="workflow_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $workflowId;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_name", type="string", length=80, nullable=false)
     */
    private $workflowName;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_description", type="text", length=65535, nullable=true)
     */
    private $workflowDescription;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_valid", type="boolean", nullable=true)
     */
    private $isValid;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_errors", type="text", length=65535, nullable=true)
     */
    private $workflowErrors;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="workflow_created_on", type="datetime", nullable=true)
     */
    private $workflowCreatedOn;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="workflow_created_by", referencedColumnName="id")
     * })
     */
    private $workflowCreatedBy;



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
     * @return NmtWfWorkflow
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
     * Set workflowDescription
     *
     * @param string $workflowDescription
     *
     * @return NmtWfWorkflow
     */
    public function setWorkflowDescription($workflowDescription)
    {
        $this->workflowDescription = $workflowDescription;

        return $this;
    }

    /**
     * Get workflowDescription
     *
     * @return string
     */
    public function getWorkflowDescription()
    {
        return $this->workflowDescription;
    }

    /**
     * Set isValid
     *
     * @param boolean $isValid
     *
     * @return NmtWfWorkflow
     */
    public function setIsValid($isValid)
    {
        $this->isValid = $isValid;

        return $this;
    }

    /**
     * Get isValid
     *
     * @return boolean
     */
    public function getIsValid()
    {
        return $this->isValid;
    }

    /**
     * Set workflowErrors
     *
     * @param string $workflowErrors
     *
     * @return NmtWfWorkflow
     */
    public function setWorkflowErrors($workflowErrors)
    {
        $this->workflowErrors = $workflowErrors;

        return $this;
    }

    /**
     * Get workflowErrors
     *
     * @return string
     */
    public function getWorkflowErrors()
    {
        return $this->workflowErrors;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return NmtWfWorkflow
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;

        return $this;
    }

    /**
     * Get startDate
     *
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * Set endDate
     *
     * @param \DateTime $endDate
     *
     * @return NmtWfWorkflow
     */
    public function setEndDate($endDate)
    {
        $this->endDate = $endDate;

        return $this;
    }

    /**
     * Get endDate
     *
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * Set workflowCreatedOn
     *
     * @param \DateTime $workflowCreatedOn
     *
     * @return NmtWfWorkflow
     */
    public function setWorkflowCreatedOn($workflowCreatedOn)
    {
        $this->workflowCreatedOn = $workflowCreatedOn;

        return $this;
    }

    /**
     * Get workflowCreatedOn
     *
     * @return \DateTime
     */
    public function getWorkflowCreatedOn()
    {
        return $this->workflowCreatedOn;
    }

    /**
     * Set workflowCreatedBy
     *
     * @param \Application\Entity\MlaUsers $workflowCreatedBy
     *
     * @return NmtWfWorkflow
     */
    public function setWorkflowCreatedBy(\Application\Entity\MlaUsers $workflowCreatedBy = null)
    {
        $this->workflowCreatedBy = $workflowCreatedBy;

        return $this;
    }

    /**
     * Get workflowCreatedBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getWorkflowCreatedBy()
    {
        return $this->workflowCreatedBy;
    }
}

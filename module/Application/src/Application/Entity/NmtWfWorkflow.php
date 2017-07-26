<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtWfWorkflow
 *
 * @ORM\Table(name="nmt_wf_workflow", indexes={@ORM\Index(name="nmt_wf_workflow_idx", columns={"created_by"})})
 * @ORM\Entity
 */
class NmtWfWorkflow
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
     * @ORM\Column(name="workflow_name", type="string", length=100, nullable=false)
     */
    private $workflowName;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_factory", type="string", length=200, nullable=false)
     */
    private $workflowFactory;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_class", type="string", length=200, nullable=false)
     */
    private $workflowClass;

    /**
     * @var string
     *
     * @ORM\Column(name="subject_class", type="string", length=200, nullable=false)
     */
    private $subjectClass;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_valid", type="boolean", nullable=true)
     */
    private $isValid;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_errors", type="string", length=255, nullable=true)
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
     * Set token
     *
     * @param string $token
     *
     * @return NmtWfWorkflow
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
     * Set workflowFactory
     *
     * @param string $workflowFactory
     *
     * @return NmtWfWorkflow
     */
    public function setWorkflowFactory($workflowFactory)
    {
        $this->workflowFactory = $workflowFactory;

        return $this;
    }

    /**
     * Get workflowFactory
     *
     * @return string
     */
    public function getWorkflowFactory()
    {
        return $this->workflowFactory;
    }

    /**
     * Set workflowClass
     *
     * @param string $workflowClass
     *
     * @return NmtWfWorkflow
     */
    public function setWorkflowClass($workflowClass)
    {
        $this->workflowClass = $workflowClass;

        return $this;
    }

    /**
     * Get workflowClass
     *
     * @return string
     */
    public function getWorkflowClass()
    {
        return $this->workflowClass;
    }

    /**
     * Set subjectClass
     *
     * @param string $subjectClass
     *
     * @return NmtWfWorkflow
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtWfWorkflow
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
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtWfWorkflow
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
     * @return NmtWfWorkflow
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtWfWorkflow
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

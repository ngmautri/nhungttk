<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtWfCase
 *
 * @ORM\Table(name="nmt_wf_case", indexes={@ORM\Index(name="nmt_wf_case_FK1_idx", columns={"workflow_id"}), @ORM\Index(name="nmt_wf_case_FK2_idx", columns={"case_created_by"})})
 * @ORM\Entity
 */
class NmtWfCase
{
    /**
     * @var integer
     *
     * @ORM\Column(name="case_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $caseId;

    /**
     * @var string
     *
     * @ORM\Column(name="context", type="string", length=45, nullable=true)
     */
    private $context;

    /**
     * @var string
     *
     * @ORM\Column(name="case_status", type="string", length=45, nullable=true)
     */
    private $caseStatus;

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
     * @ORM\Column(name="case_created_on", type="datetime", nullable=true)
     */
    private $caseCreatedOn;

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
     *   @ORM\JoinColumn(name="case_created_by", referencedColumnName="id")
     * })
     */
    private $caseCreatedBy;



    /**
     * Get caseId
     *
     * @return integer
     */
    public function getCaseId()
    {
        return $this->caseId;
    }

    /**
     * Set context
     *
     * @param string $context
     *
     * @return NmtWfCase
     */
    public function setContext($context)
    {
        $this->context = $context;

        return $this;
    }

    /**
     * Get context
     *
     * @return string
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * Set caseStatus
     *
     * @param string $caseStatus
     *
     * @return NmtWfCase
     */
    public function setCaseStatus($caseStatus)
    {
        $this->caseStatus = $caseStatus;

        return $this;
    }

    /**
     * Get caseStatus
     *
     * @return string
     */
    public function getCaseStatus()
    {
        return $this->caseStatus;
    }

    /**
     * Set startDate
     *
     * @param \DateTime $startDate
     *
     * @return NmtWfCase
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
     * @return NmtWfCase
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
     * Set caseCreatedOn
     *
     * @param \DateTime $caseCreatedOn
     *
     * @return NmtWfCase
     */
    public function setCaseCreatedOn($caseCreatedOn)
    {
        $this->caseCreatedOn = $caseCreatedOn;

        return $this;
    }

    /**
     * Get caseCreatedOn
     *
     * @return \DateTime
     */
    public function getCaseCreatedOn()
    {
        return $this->caseCreatedOn;
    }

    /**
     * Set workflow
     *
     * @param \Application\Entity\NmtWfWorkflow $workflow
     *
     * @return NmtWfCase
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
     * Set caseCreatedBy
     *
     * @param \Application\Entity\MlaUsers $caseCreatedBy
     *
     * @return NmtWfCase
     */
    public function setCaseCreatedBy(\Application\Entity\MlaUsers $caseCreatedBy = null)
    {
        $this->caseCreatedBy = $caseCreatedBy;

        return $this;
    }

    /**
     * Get caseCreatedBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getCaseCreatedBy()
    {
        return $this->caseCreatedBy;
    }
}

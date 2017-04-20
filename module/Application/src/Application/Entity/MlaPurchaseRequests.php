<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * MlaPurchaseRequests
 *
 * @ORM\Table(name="mla_purchase_requests", indexes={@ORM\Index(name="mla_purchase_requests_FK1_idx", columns={"requested_by"}), @ORM\Index(name="mla_purchase_requests_FK2_idx", columns={"last_workflow_id"})})
 * @ORM\Entity
 */
class MlaPurchaseRequests
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
     * @var integer
     *
     * @ORM\Column(name="seq_number_of_year", type="integer", nullable=false)
     */
    private $seqNumberOfYear;

    /**
     * @var string
     *
     * @ORM\Column(name="auto_pr_number", type="string", length=100, nullable=false)
     */
    private $autoPrNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="pr_number", type="string", length=100, nullable=true)
     */
    private $prNumber;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=50, nullable=true)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", length=65535, nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="requested_on", type="datetime", nullable=true)
     */
    private $requestedOn;

    /**
     * @var string
     *
     * @ORM\Column(name="verified_by", type="string", length=150, nullable=true)
     */
    private $verifiedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="verified_on", type="datetime", nullable=true)
     */
    private $verifiedOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="approved_by", type="integer", nullable=true)
     */
    private $approvedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="approved_on", type="datetime", nullable=true)
     */
    private $approvedOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="released_by", type="integer", nullable=true)
     */
    private $releasedBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="released_on", type="datetime", nullable=true)
     */
    private $releasedOn;

    /**
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="requested_by", referencedColumnName="id")
     * })
     */
    private $requestedBy;

    /**
     * @var \Application\Entity\MlaPurchaseRequestsWorkflows
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaPurchaseRequestsWorkflows")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_workflow_id", referencedColumnName="id")
     * })
     */
    private $lastWorkflow;



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
     * Set seqNumberOfYear
     *
     * @param integer $seqNumberOfYear
     *
     * @return MlaPurchaseRequests
     */
    public function setSeqNumberOfYear($seqNumberOfYear)
    {
        $this->seqNumberOfYear = $seqNumberOfYear;

        return $this;
    }

    /**
     * Get seqNumberOfYear
     *
     * @return integer
     */
    public function getSeqNumberOfYear()
    {
        return $this->seqNumberOfYear;
    }

    /**
     * Set autoPrNumber
     *
     * @param string $autoPrNumber
     *
     * @return MlaPurchaseRequests
     */
    public function setAutoPrNumber($autoPrNumber)
    {
        $this->autoPrNumber = $autoPrNumber;

        return $this;
    }

    /**
     * Get autoPrNumber
     *
     * @return string
     */
    public function getAutoPrNumber()
    {
        return $this->autoPrNumber;
    }

    /**
     * Set prNumber
     *
     * @param string $prNumber
     *
     * @return MlaPurchaseRequests
     */
    public function setPrNumber($prNumber)
    {
        $this->prNumber = $prNumber;

        return $this;
    }

    /**
     * Get prNumber
     *
     * @return string
     */
    public function getPrNumber()
    {
        return $this->prNumber;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return MlaPurchaseRequests
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return MlaPurchaseRequests
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set requestedOn
     *
     * @param \DateTime $requestedOn
     *
     * @return MlaPurchaseRequests
     */
    public function setRequestedOn($requestedOn)
    {
        $this->requestedOn = $requestedOn;

        return $this;
    }

    /**
     * Get requestedOn
     *
     * @return \DateTime
     */
    public function getRequestedOn()
    {
        return $this->requestedOn;
    }

    /**
     * Set verifiedBy
     *
     * @param string $verifiedBy
     *
     * @return MlaPurchaseRequests
     */
    public function setVerifiedBy($verifiedBy)
    {
        $this->verifiedBy = $verifiedBy;

        return $this;
    }

    /**
     * Get verifiedBy
     *
     * @return string
     */
    public function getVerifiedBy()
    {
        return $this->verifiedBy;
    }

    /**
     * Set verifiedOn
     *
     * @param \DateTime $verifiedOn
     *
     * @return MlaPurchaseRequests
     */
    public function setVerifiedOn($verifiedOn)
    {
        $this->verifiedOn = $verifiedOn;

        return $this;
    }

    /**
     * Get verifiedOn
     *
     * @return \DateTime
     */
    public function getVerifiedOn()
    {
        return $this->verifiedOn;
    }

    /**
     * Set approvedBy
     *
     * @param integer $approvedBy
     *
     * @return MlaPurchaseRequests
     */
    public function setApprovedBy($approvedBy)
    {
        $this->approvedBy = $approvedBy;

        return $this;
    }

    /**
     * Get approvedBy
     *
     * @return integer
     */
    public function getApprovedBy()
    {
        return $this->approvedBy;
    }

    /**
     * Set approvedOn
     *
     * @param \DateTime $approvedOn
     *
     * @return MlaPurchaseRequests
     */
    public function setApprovedOn($approvedOn)
    {
        $this->approvedOn = $approvedOn;

        return $this;
    }

    /**
     * Get approvedOn
     *
     * @return \DateTime
     */
    public function getApprovedOn()
    {
        return $this->approvedOn;
    }

    /**
     * Set releasedBy
     *
     * @param integer $releasedBy
     *
     * @return MlaPurchaseRequests
     */
    public function setReleasedBy($releasedBy)
    {
        $this->releasedBy = $releasedBy;

        return $this;
    }

    /**
     * Get releasedBy
     *
     * @return integer
     */
    public function getReleasedBy()
    {
        return $this->releasedBy;
    }

    /**
     * Set releasedOn
     *
     * @param \DateTime $releasedOn
     *
     * @return MlaPurchaseRequests
     */
    public function setReleasedOn($releasedOn)
    {
        $this->releasedOn = $releasedOn;

        return $this;
    }

    /**
     * Get releasedOn
     *
     * @return \DateTime
     */
    public function getReleasedOn()
    {
        return $this->releasedOn;
    }

    /**
     * Set requestedBy
     *
     * @param \Application\Entity\MlaUsers $requestedBy
     *
     * @return MlaPurchaseRequests
     */
    public function setRequestedBy(\Application\Entity\MlaUsers $requestedBy = null)
    {
        $this->requestedBy = $requestedBy;

        return $this;
    }

    /**
     * Get requestedBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getRequestedBy()
    {
        return $this->requestedBy;
    }

    /**
     * Set lastWorkflow
     *
     * @param \Application\Entity\MlaPurchaseRequestsWorkflows $lastWorkflow
     *
     * @return MlaPurchaseRequests
     */
    public function setLastWorkflow(\Application\Entity\MlaPurchaseRequestsWorkflows $lastWorkflow = null)
    {
        $this->lastWorkflow = $lastWorkflow;

        return $this;
    }

    /**
     * Get lastWorkflow
     *
     * @return \Application\Entity\MlaPurchaseRequestsWorkflows
     */
    public function getLastWorkflow()
    {
        return $this->lastWorkflow;
    }
}

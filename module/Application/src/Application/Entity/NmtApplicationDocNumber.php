<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationDocNumber
 *
 * @ORM\Table(name="nmt_application_doc_number", indexes={@ORM\Index(name="nmt_application_doc_number_idx", columns={"created_by"}), @ORM\Index(name="nmt_application_doc_number_FK2_idx", columns={"lastchange_by"})})
 * @ORM\Entity
 */
class NmtApplicationDocNumber
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
     * @ORM\Column(name="company_id", type="integer", nullable=false)
     */
    private $companyId;

    /**
     * @var integer
     *
     * @ORM\Column(name="range_no", type="integer", nullable=true)
     */
    private $rangeNo;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="integer", nullable=true)
     */
    private $year;

    /**
     * @var integer
     *
     * @ORM\Column(name="from_number", type="integer", nullable=false)
     */
    private $fromNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="to_number", type="integer", nullable=false)
     */
    private $toNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="current_number", type="integer", nullable=true)
     */
    private $currentNumber;

    /**
     * @var integer
     *
     * @ORM\Column(name="numeric_size", type="integer", nullable=true)
     */
    private $numericSize;

    /**
     * @var string
     *
     * @ORM\Column(name="prefix", type="string", length=10, nullable=true)
     */
    private $prefix;

    /**
     * @var string
     *
     * @ORM\Column(name="suffix", type="string", length=10, nullable=true)
     */
    private $suffix;

    /**
     * @var string
     *
     * @ORM\Column(name="status", type="string", length=45, nullable=true)
     */
    private $status;

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
     * @ORM\Column(name="doc_number_name", type="string", length=45, nullable=true)
     */
    private $docNumberName;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_number_name1", type="string", length=45, nullable=true)
     */
    private $docNumberName1;

    /**
     * @var string
     *
     * @ORM\Column(name="doc_number_code", type="string", length=45, nullable=true)
     */
    private $docNumberCode;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="valid_from", type="datetime", nullable=true)
     */
    private $validFrom;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="valid_to", type="datetime", nullable=true)
     */
    private $validTo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="issuing_date", type="datetime", nullable=true)
     */
    private $issuingDate;

    /**
     * @var string
     *
     * @ORM\Column(name="current_state", type="string", length=45, nullable=true)
     */
    private $currentState;

    /**
     * @var string
     *
     * @ORM\Column(name="subject_class", type="string", length=200, nullable=true)
     */
    private $subjectClass;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
     */
    private $remarks;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastchange_on", type="datetime", nullable=true)
     */
    private $lastchangeOn;

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
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="lastchange_by", referencedColumnName="id")
     * })
     */
    private $lastchangeBy;



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
     * Set companyId
     *
     * @param integer $companyId
     *
     * @return NmtApplicationDocNumber
     */
    public function setCompanyId($companyId)
    {
        $this->companyId = $companyId;

        return $this;
    }

    /**
     * Get companyId
     *
     * @return integer
     */
    public function getCompanyId()
    {
        return $this->companyId;
    }

    /**
     * Set rangeNo
     *
     * @param integer $rangeNo
     *
     * @return NmtApplicationDocNumber
     */
    public function setRangeNo($rangeNo)
    {
        $this->rangeNo = $rangeNo;

        return $this;
    }

    /**
     * Get rangeNo
     *
     * @return integer
     */
    public function getRangeNo()
    {
        return $this->rangeNo;
    }

    /**
     * Set year
     *
     * @param integer $year
     *
     * @return NmtApplicationDocNumber
     */
    public function setYear($year)
    {
        $this->year = $year;

        return $this;
    }

    /**
     * Get year
     *
     * @return integer
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * Set fromNumber
     *
     * @param integer $fromNumber
     *
     * @return NmtApplicationDocNumber
     */
    public function setFromNumber($fromNumber)
    {
        $this->fromNumber = $fromNumber;

        return $this;
    }

    /**
     * Get fromNumber
     *
     * @return integer
     */
    public function getFromNumber()
    {
        return $this->fromNumber;
    }

    /**
     * Set toNumber
     *
     * @param integer $toNumber
     *
     * @return NmtApplicationDocNumber
     */
    public function setToNumber($toNumber)
    {
        $this->toNumber = $toNumber;

        return $this;
    }

    /**
     * Get toNumber
     *
     * @return integer
     */
    public function getToNumber()
    {
        return $this->toNumber;
    }

    /**
     * Set currentNumber
     *
     * @param integer $currentNumber
     *
     * @return NmtApplicationDocNumber
     */
    public function setCurrentNumber($currentNumber)
    {
        $this->currentNumber = $currentNumber;

        return $this;
    }

    /**
     * Get currentNumber
     *
     * @return integer
     */
    public function getCurrentNumber()
    {
        return $this->currentNumber;
    }

    /**
     * Set numericSize
     *
     * @param integer $numericSize
     *
     * @return NmtApplicationDocNumber
     */
    public function setNumericSize($numericSize)
    {
        $this->numericSize = $numericSize;

        return $this;
    }

    /**
     * Get numericSize
     *
     * @return integer
     */
    public function getNumericSize()
    {
        return $this->numericSize;
    }

    /**
     * Set prefix
     *
     * @param string $prefix
     *
     * @return NmtApplicationDocNumber
     */
    public function setPrefix($prefix)
    {
        $this->prefix = $prefix;

        return $this;
    }

    /**
     * Get prefix
     *
     * @return string
     */
    public function getPrefix()
    {
        return $this->prefix;
    }

    /**
     * Set suffix
     *
     * @param string $suffix
     *
     * @return NmtApplicationDocNumber
     */
    public function setSuffix($suffix)
    {
        $this->suffix = $suffix;

        return $this;
    }

    /**
     * Get suffix
     *
     * @return string
     */
    public function getSuffix()
    {
        return $this->suffix;
    }

    /**
     * Set status
     *
     * @param string $status
     *
     * @return NmtApplicationDocNumber
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return string
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtApplicationDocNumber
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
     * @return NmtApplicationDocNumber
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
     * Set docNumberName
     *
     * @param string $docNumberName
     *
     * @return NmtApplicationDocNumber
     */
    public function setDocNumberName($docNumberName)
    {
        $this->docNumberName = $docNumberName;

        return $this;
    }

    /**
     * Get docNumberName
     *
     * @return string
     */
    public function getDocNumberName()
    {
        return $this->docNumberName;
    }

    /**
     * Set docNumberName1
     *
     * @param string $docNumberName1
     *
     * @return NmtApplicationDocNumber
     */
    public function setDocNumberName1($docNumberName1)
    {
        $this->docNumberName1 = $docNumberName1;

        return $this;
    }

    /**
     * Get docNumberName1
     *
     * @return string
     */
    public function getDocNumberName1()
    {
        return $this->docNumberName1;
    }

    /**
     * Set docNumberCode
     *
     * @param string $docNumberCode
     *
     * @return NmtApplicationDocNumber
     */
    public function setDocNumberCode($docNumberCode)
    {
        $this->docNumberCode = $docNumberCode;

        return $this;
    }

    /**
     * Get docNumberCode
     *
     * @return string
     */
    public function getDocNumberCode()
    {
        return $this->docNumberCode;
    }

    /**
     * Set validFrom
     *
     * @param \DateTime $validFrom
     *
     * @return NmtApplicationDocNumber
     */
    public function setValidFrom($validFrom)
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    /**
     * Get validFrom
     *
     * @return \DateTime
     */
    public function getValidFrom()
    {
        return $this->validFrom;
    }

    /**
     * Set validTo
     *
     * @param \DateTime $validTo
     *
     * @return NmtApplicationDocNumber
     */
    public function setValidTo($validTo)
    {
        $this->validTo = $validTo;

        return $this;
    }

    /**
     * Get validTo
     *
     * @return \DateTime
     */
    public function getValidTo()
    {
        return $this->validTo;
    }

    /**
     * Set issuingDate
     *
     * @param \DateTime $issuingDate
     *
     * @return NmtApplicationDocNumber
     */
    public function setIssuingDate($issuingDate)
    {
        $this->issuingDate = $issuingDate;

        return $this;
    }

    /**
     * Get issuingDate
     *
     * @return \DateTime
     */
    public function getIssuingDate()
    {
        return $this->issuingDate;
    }

    /**
     * Set currentState
     *
     * @param string $currentState
     *
     * @return NmtApplicationDocNumber
     */
    public function setCurrentState($currentState)
    {
        $this->currentState = $currentState;

        return $this;
    }

    /**
     * Get currentState
     *
     * @return string
     */
    public function getCurrentState()
    {
        return $this->currentState;
    }

    /**
     * Set subjectClass
     *
     * @param string $subjectClass
     *
     * @return NmtApplicationDocNumber
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
     * @return NmtApplicationDocNumber
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
     * Set token
     *
     * @param string $token
     *
     * @return NmtApplicationDocNumber
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
     * Set lastchangeOn
     *
     * @param \DateTime $lastchangeOn
     *
     * @return NmtApplicationDocNumber
     */
    public function setLastchangeOn($lastchangeOn)
    {
        $this->lastchangeOn = $lastchangeOn;

        return $this;
    }

    /**
     * Get lastchangeOn
     *
     * @return \DateTime
     */
    public function getLastchangeOn()
    {
        return $this->lastchangeOn;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtApplicationDocNumber
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
     * Set lastchangeBy
     *
     * @param \Application\Entity\MlaUsers $lastchangeBy
     *
     * @return NmtApplicationDocNumber
     */
    public function setLastchangeBy(\Application\Entity\MlaUsers $lastchangeBy = null)
    {
        $this->lastchangeBy = $lastchangeBy;

        return $this;
    }

    /**
     * Get lastchangeBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getLastchangeBy()
    {
        return $this->lastchangeBy;
    }
}

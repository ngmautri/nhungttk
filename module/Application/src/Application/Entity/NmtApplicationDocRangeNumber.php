<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationDocRangeNumber
 *
 * @ORM\Table(name="nmt_application_doc_range_number")
 * @ORM\Entity
 */
class NmtApplicationDocRangeNumber
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
     * @ORM\Column(name="range_no", type="integer", nullable=false)
     */
    private $rangeNo;

    /**
     * @var integer
     *
     * @ORM\Column(name="year", type="integer", nullable=false)
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
     * @ORM\Column(name="is_activated", type="boolean", nullable=true)
     */
    private $isActivated;

    /**
     * @var integer
     *
     * @ORM\Column(name="created_by", type="integer", nullable=false)
     */
    private $createdBy;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;



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
     * @return NmtApplicationDocRangeNumber
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
     * @return NmtApplicationDocRangeNumber
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
     * @return NmtApplicationDocRangeNumber
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
     * @return NmtApplicationDocRangeNumber
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
     * @return NmtApplicationDocRangeNumber
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
     * @return NmtApplicationDocRangeNumber
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
     * @return NmtApplicationDocRangeNumber
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
     * @return NmtApplicationDocRangeNumber
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
     * @return NmtApplicationDocRangeNumber
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
     * @return NmtApplicationDocRangeNumber
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
     * Set isActivated
     *
     * @param boolean $isActivated
     *
     * @return NmtApplicationDocRangeNumber
     */
    public function setIsActivated($isActivated)
    {
        $this->isActivated = $isActivated;

        return $this;
    }

    /**
     * Get isActivated
     *
     * @return boolean
     */
    public function getIsActivated()
    {
        return $this->isActivated;
    }

    /**
     * Set createdBy
     *
     * @param integer $createdBy
     *
     * @return NmtApplicationDocRangeNumber
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;

        return $this;
    }

    /**
     * Get createdBy
     *
     * @return integer
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtApplicationDocRangeNumber
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
}

<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationPmtTerm
 *
 * @ORM\Table(name="nmt_application_pmt_term", indexes={@ORM\Index(name="nmt_application_pmt_term_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_application_pmt_term_FK2_idx", columns={"last_change_by"})})
 * @ORM\Entity
 */
class NmtApplicationPmtTerm
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
     * @ORM\Column(name="pmt_term_code", type="string", length=45, nullable=true)
     */
    private $pmtTermCode;

    /**
     * @var string
     *
     * @ORM\Column(name="pmt_term_name", type="string", length=45, nullable=true)
     */
    private $pmtTermName;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_change_on", type="datetime", nullable=true)
     */
    private $lastChangeOn;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision_no", type="integer", nullable=true)
     */
    private $revisionNo;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_prepayment", type="boolean", nullable=true)
     */
    private $isPrepayment;

    /**
     * @var integer
     *
     * @ORM\Column(name="prepayment_percentage", type="integer", nullable=true)
     */
    private $prepaymentPercentage;

    /**
     * @var boolean
     *
     * @ORM\Column(name="pay_after_shipment", type="boolean", nullable=true)
     */
    private $payAfterShipment;

    /**
     * @var boolean
     *
     * @ORM\Column(name="pay_after_invoice", type="boolean", nullable=true)
     */
    private $payAfterInvoice;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

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
     *   @ORM\JoinColumn(name="last_change_by", referencedColumnName="id")
     * })
     */
    private $lastChangeBy;



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
     * Set pmtTermCode
     *
     * @param string $pmtTermCode
     *
     * @return NmtApplicationPmtTerm
     */
    public function setPmtTermCode($pmtTermCode)
    {
        $this->pmtTermCode = $pmtTermCode;

        return $this;
    }

    /**
     * Get pmtTermCode
     *
     * @return string
     */
    public function getPmtTermCode()
    {
        return $this->pmtTermCode;
    }

    /**
     * Set pmtTermName
     *
     * @param string $pmtTermName
     *
     * @return NmtApplicationPmtTerm
     */
    public function setPmtTermName($pmtTermName)
    {
        $this->pmtTermName = $pmtTermName;

        return $this;
    }

    /**
     * Get pmtTermName
     *
     * @return string
     */
    public function getPmtTermName()
    {
        return $this->pmtTermName;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return NmtApplicationPmtTerm
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
     * Set status
     *
     * @param boolean $status
     *
     * @return NmtApplicationPmtTerm
     */
    public function setStatus($status)
    {
        $this->status = $status;

        return $this;
    }

    /**
     * Get status
     *
     * @return boolean
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return NmtApplicationPmtTerm
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
     * Set lastChangeOn
     *
     * @param \DateTime $lastChangeOn
     *
     * @return NmtApplicationPmtTerm
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;

        return $this;
    }

    /**
     * Get lastChangeOn
     *
     * @return \DateTime
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return NmtApplicationPmtTerm
     */
    public function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;

        return $this;
    }

    /**
     * Get revisionNo
     *
     * @return integer
     */
    public function getRevisionNo()
    {
        return $this->revisionNo;
    }

    /**
     * Set token
     *
     * @param string $token
     *
     * @return NmtApplicationPmtTerm
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
     * Set isPrepayment
     *
     * @param boolean $isPrepayment
     *
     * @return NmtApplicationPmtTerm
     */
    public function setIsPrepayment($isPrepayment)
    {
        $this->isPrepayment = $isPrepayment;

        return $this;
    }

    /**
     * Get isPrepayment
     *
     * @return boolean
     */
    public function getIsPrepayment()
    {
        return $this->isPrepayment;
    }

    /**
     * Set prepaymentPercentage
     *
     * @param integer $prepaymentPercentage
     *
     * @return NmtApplicationPmtTerm
     */
    public function setPrepaymentPercentage($prepaymentPercentage)
    {
        $this->prepaymentPercentage = $prepaymentPercentage;

        return $this;
    }

    /**
     * Get prepaymentPercentage
     *
     * @return integer
     */
    public function getPrepaymentPercentage()
    {
        return $this->prepaymentPercentage;
    }

    /**
     * Set payAfterShipment
     *
     * @param boolean $payAfterShipment
     *
     * @return NmtApplicationPmtTerm
     */
    public function setPayAfterShipment($payAfterShipment)
    {
        $this->payAfterShipment = $payAfterShipment;

        return $this;
    }

    /**
     * Get payAfterShipment
     *
     * @return boolean
     */
    public function getPayAfterShipment()
    {
        return $this->payAfterShipment;
    }

    /**
     * Set payAfterInvoice
     *
     * @param boolean $payAfterInvoice
     *
     * @return NmtApplicationPmtTerm
     */
    public function setPayAfterInvoice($payAfterInvoice)
    {
        $this->payAfterInvoice = $payAfterInvoice;

        return $this;
    }

    /**
     * Get payAfterInvoice
     *
     * @return boolean
     */
    public function getPayAfterInvoice()
    {
        return $this->payAfterInvoice;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtApplicationPmtTerm
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtApplicationPmtTerm
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
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return NmtApplicationPmtTerm
     */
    public function setLastChangeBy(\Application\Entity\MlaUsers $lastChangeBy = null)
    {
        $this->lastChangeBy = $lastChangeBy;

        return $this;
    }

    /**
     * Get lastChangeBy
     *
     * @return \Application\Entity\MlaUsers
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }
}

<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * FinFx
 *
 * @ORM\Table(name="fin_fx", indexes={@ORM\Index(name="fin_fx_FK1_idx", columns={"source_currency"}), @ORM\Index(name="fin_fx_FK2_idx", columns={"target_currency"}), @ORM\Index(name="fin_fx_FK3_idx", columns={"company_id"}), @ORM\Index(name="fin_fx_FK4_idx", columns={"created_by"}), @ORM\Index(name="fin_fx_FK5_idx", columns={"lastchange_by"})})
 * @ORM\Entity
 */
class FinFx
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
     * @var \DateTime
     *
     * @ORM\Column(name="fx_date", type="datetime", nullable=true)
     */
    private $fxDate;

    /**
     * @var string
     *
     * @ORM\Column(name="fx_rate", type="decimal", precision=14, scale=5, nullable=true)
     */
    private $fxRate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=true)
     */
    private $createdOn;

    /**
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

    /**
     * @var string
     *
     * @ORM\Column(name="remarks", type="string", length=100, nullable=true)
     */
    private $remarks;

    /**
     * @var integer
     *
     * @ORM\Column(name="revision_no", type="integer", nullable=true)
     */
    private $revisionNo;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="lastchange_on", type="datetime", nullable=true)
     */
    private $lastchangeOn;

    /**
     * @var \Application\Entity\NmtApplicationCurrency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="source_currency", referencedColumnName="id")
     * })
     */
    private $sourceCurrency;

    /**
     * @var \Application\Entity\NmtApplicationCurrency
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCurrency")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="target_currency", referencedColumnName="id")
     * })
     */
    private $targetCurrency;

    /**
     * @var \Application\Entity\NmtApplicationCompany
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCompany")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;

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
     * Set fxDate
     *
     * @param \DateTime $fxDate
     *
     * @return FinFx
     */
    public function setFxDate($fxDate)
    {
        $this->fxDate = $fxDate;

        return $this;
    }

    /**
     * Get fxDate
     *
     * @return \DateTime
     */
    public function getFxDate()
    {
        return $this->fxDate;
    }

    /**
     * Set fxRate
     *
     * @param string $fxRate
     *
     * @return FinFx
     */
    public function setFxRate($fxRate)
    {
        $this->fxRate = $fxRate;

        return $this;
    }

    /**
     * Get fxRate
     *
     * @return string
     */
    public function getFxRate()
    {
        return $this->fxRate;
    }

    /**
     * Set createdOn
     *
     * @param \DateTime $createdOn
     *
     * @return FinFx
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
     * Set token
     *
     * @param string $token
     *
     * @return FinFx
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
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return FinFx
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
     * Set remarks
     *
     * @param string $remarks
     *
     * @return FinFx
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
     * Set revisionNo
     *
     * @param integer $revisionNo
     *
     * @return FinFx
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
     * Set lastchangeOn
     *
     * @param \DateTime $lastchangeOn
     *
     * @return FinFx
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
     * Set sourceCurrency
     *
     * @param \Application\Entity\NmtApplicationCurrency $sourceCurrency
     *
     * @return FinFx
     */
    public function setSourceCurrency(\Application\Entity\NmtApplicationCurrency $sourceCurrency = null)
    {
        $this->sourceCurrency = $sourceCurrency;

        return $this;
    }

    /**
     * Get sourceCurrency
     *
     * @return \Application\Entity\NmtApplicationCurrency
     */
    public function getSourceCurrency()
    {
        return $this->sourceCurrency;
    }

    /**
     * Set targetCurrency
     *
     * @param \Application\Entity\NmtApplicationCurrency $targetCurrency
     *
     * @return FinFx
     */
    public function setTargetCurrency(\Application\Entity\NmtApplicationCurrency $targetCurrency = null)
    {
        $this->targetCurrency = $targetCurrency;

        return $this;
    }

    /**
     * Get targetCurrency
     *
     * @return \Application\Entity\NmtApplicationCurrency
     */
    public function getTargetCurrency()
    {
        return $this->targetCurrency;
    }

    /**
     * Set company
     *
     * @param \Application\Entity\NmtApplicationCompany $company
     *
     * @return FinFx
     */
    public function setCompany(\Application\Entity\NmtApplicationCompany $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Application\Entity\NmtApplicationCompany
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return FinFx
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
     * @return FinFx
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

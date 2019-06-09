<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationUom
 *
 * @ORM\Table(name="nmt_application_uom", uniqueConstraints={@ORM\UniqueConstraint(name="uom_name_UNIQUE", columns={"uom_name"})}, indexes={@ORM\Index(name="nmt_application_uom_FK1_idx", columns={"created_by"}), @ORM\Index(name="nmt_application_uom_FK2_idx", columns={"company_id"})})
 * @ORM\Entity
 */
class NmtApplicationUom
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
     * @ORM\Column(name="uom_code", type="string", length=45, nullable=false)
     */
    private $uomCode;

    /**
     * @var string
     *
     * @ORM\Column(name="uom_name", type="string", length=100, nullable=true)
     */
    private $uomName;

    /**
     * @var string
     *
     * @ORM\Column(name="uom_description", type="text", length=65535, nullable=true)
     */
    private $uomDescription;

    /**
     * @var string
     *
     * @ORM\Column(name="conversion_factor", type="string", length=45, nullable=true)
     */
    private $conversionFactor;

    /**
     * @var string
     *
     * @ORM\Column(name="sector", type="string", length=45, nullable=true)
     */
    private $sector;

    /**
     * @var string
     *
     * @ORM\Column(name="symbol", type="string", length=45, nullable=true)
     */
    private $symbol;

    /**
     * @var boolean
     *
     * @ORM\Column(name="status", type="boolean", nullable=true)
     */
    private $status;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="created_on", type="datetime", nullable=false)
     */
    private $createdOn = 'CURRENT_TIMESTAMP';

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
     * @var \Application\Entity\NmtApplicationCompany
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCompany")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="company_id", referencedColumnName="id")
     * })
     */
    private $company;



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
     * Set uomCode
     *
     * @param string $uomCode
     *
     * @return NmtApplicationUom
     */
    public function setUomCode($uomCode)
    {
        $this->uomCode = $uomCode;

        return $this;
    }

    /**
     * Get uomCode
     *
     * @return string
     */
    public function getUomCode()
    {
        return $this->uomCode;
    }

    /**
     * Set uomName
     *
     * @param string $uomName
     *
     * @return NmtApplicationUom
     */
    public function setUomName($uomName)
    {
        $this->uomName = $uomName;

        return $this;
    }

    /**
     * Get uomName
     *
     * @return string
     */
    public function getUomName()
    {
        return $this->uomName;
    }

    /**
     * Set uomDescription
     *
     * @param string $uomDescription
     *
     * @return NmtApplicationUom
     */
    public function setUomDescription($uomDescription)
    {
        $this->uomDescription = $uomDescription;

        return $this;
    }

    /**
     * Get uomDescription
     *
     * @return string
     */
    public function getUomDescription()
    {
        return $this->uomDescription;
    }

    /**
     * Set conversionFactor
     *
     * @param string $conversionFactor
     *
     * @return NmtApplicationUom
     */
    public function setConversionFactor($conversionFactor)
    {
        $this->conversionFactor = $conversionFactor;

        return $this;
    }

    /**
     * Get conversionFactor
     *
     * @return string
     */
    public function getConversionFactor()
    {
        return $this->conversionFactor;
    }

    /**
     * Set sector
     *
     * @param string $sector
     *
     * @return NmtApplicationUom
     */
    public function setSector($sector)
    {
        $this->sector = $sector;

        return $this;
    }

    /**
     * Get sector
     *
     * @return string
     */
    public function getSector()
    {
        return $this->sector;
    }

    /**
     * Set symbol
     *
     * @param string $symbol
     *
     * @return NmtApplicationUom
     */
    public function setSymbol($symbol)
    {
        $this->symbol = $symbol;

        return $this;
    }

    /**
     * Get symbol
     *
     * @return string
     */
    public function getSymbol()
    {
        return $this->symbol;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return NmtApplicationUom
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
     * @return NmtApplicationUom
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
     * Set createdBy
     *
     * @param \Application\Entity\MlaUsers $createdBy
     *
     * @return NmtApplicationUom
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
     * Set company
     *
     * @param \Application\Entity\NmtApplicationCompany $company
     *
     * @return NmtApplicationUom
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
}

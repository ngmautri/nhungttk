<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationCurrency
 *
 * @ORM\Table(name="nmt_application_currency", uniqueConstraints={@ORM\UniqueConstraint(name="nmt_application_currency_currency_UNIQUE", columns={"currency"})}, indexes={@ORM\Index(name="nmt_application_currency_idx", columns={"created_by"})})
 * @ORM\Entity
 */
class NmtApplicationCurrency
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
     * @ORM\Column(name="currency", type="string", length=3, nullable=false)
     */
    private $currency;

    /**
     * @var integer
     *
     * @ORM\Column(name="currency_numeric_code", type="integer", nullable=true)
     */
    private $currencyNumericCode;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="string", length=255, nullable=true)
     */
    private $description;

    /**
     * @var string
     *
     * @ORM\Column(name="entity", type="string", length=45, nullable=true)
     */
    private $entity;

    /**
     * @var integer
     *
     * @ORM\Column(name="country_id", type="integer", nullable=true)
     */
    private $countryId;

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
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set currency
     *
     * @param string $currency
     *
     * @return NmtApplicationCurrency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;

        return $this;
    }

    /**
     * Get currency
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * Set currencyNumericCode
     *
     * @param integer $currencyNumericCode
     *
     * @return NmtApplicationCurrency
     */
    public function setCurrencyNumericCode($currencyNumericCode)
    {
        $this->currencyNumericCode = $currencyNumericCode;

        return $this;
    }

    /**
     * Get currencyNumericCode
     *
     * @return integer
     */
    public function getCurrencyNumericCode()
    {
        return $this->currencyNumericCode;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return NmtApplicationCurrency
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
     * Set entity
     *
     * @param string $entity
     *
     * @return NmtApplicationCurrency
     */
    public function setEntity($entity)
    {
        $this->entity = $entity;

        return $this;
    }

    /**
     * Get entity
     *
     * @return string
     */
    public function getEntity()
    {
        return $this->entity;
    }

    /**
     * Set countryId
     *
     * @param integer $countryId
     *
     * @return NmtApplicationCurrency
     */
    public function setCountryId($countryId)
    {
        $this->countryId = $countryId;

        return $this;
    }

    /**
     * Get countryId
     *
     * @return integer
     */
    public function getCountryId()
    {
        return $this->countryId;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return NmtApplicationCurrency
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
     * @return NmtApplicationCurrency
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
     * @return NmtApplicationCurrency
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
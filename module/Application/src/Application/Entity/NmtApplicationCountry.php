<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationCountry
 *
 * @ORM\Table(name="nmt_application_country", uniqueConstraints={@ORM\UniqueConstraint(name="nmt_application_country_country_name_UNIQUE", columns={"country_name"})}, indexes={@ORM\Index(name="nmt_application_country_idx", columns={"created_by"})})
 * @ORM\Entity
 */
class NmtApplicationCountry
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
     * @ORM\Column(name="country_name", type="string", length=100, nullable=true)
     */
    private $countryName;

    /**
     * @var string
     *
     * @ORM\Column(name="country_code_2", type="string", length=2, nullable=false)
     */
    private $countryCode2;

    /**
     * @var string
     *
     * @ORM\Column(name="country_code_3", type="string", length=3, nullable=false)
     */
    private $countryCode3;

    /**
     * @var integer
     *
     * @ORM\Column(name="country_numeric_code", type="integer", nullable=true)
     */
    private $countryNumericCode;

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
     * Set countryName
     *
     * @param string $countryName
     *
     * @return NmtApplicationCountry
     */
    public function setCountryName($countryName)
    {
        $this->countryName = $countryName;

        return $this;
    }

    /**
     * Get countryName
     *
     * @return string
     */
    public function getCountryName()
    {
        return $this->countryName;
    }

    /**
     * Set countryCode2
     *
     * @param string $countryCode2
     *
     * @return NmtApplicationCountry
     */
    public function setCountryCode2($countryCode2)
    {
        $this->countryCode2 = $countryCode2;

        return $this;
    }

    /**
     * Get countryCode2
     *
     * @return string
     */
    public function getCountryCode2()
    {
        return $this->countryCode2;
    }

    /**
     * Set countryCode3
     *
     * @param string $countryCode3
     *
     * @return NmtApplicationCountry
     */
    public function setCountryCode3($countryCode3)
    {
        $this->countryCode3 = $countryCode3;

        return $this;
    }

    /**
     * Get countryCode3
     *
     * @return string
     */
    public function getCountryCode3()
    {
        return $this->countryCode3;
    }

    /**
     * Set countryNumericCode
     *
     * @param integer $countryNumericCode
     *
     * @return NmtApplicationCountry
     */
    public function setCountryNumericCode($countryNumericCode)
    {
        $this->countryNumericCode = $countryNumericCode;

        return $this;
    }

    /**
     * Get countryNumericCode
     *
     * @return integer
     */
    public function getCountryNumericCode()
    {
        return $this->countryNumericCode;
    }

    /**
     * Set status
     *
     * @param boolean $status
     *
     * @return NmtApplicationCountry
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
     * @return NmtApplicationCountry
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
     * @return NmtApplicationCountry
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

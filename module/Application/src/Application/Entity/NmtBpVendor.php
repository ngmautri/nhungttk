<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtBpVendor
 *
 * @ORM\Table(name="nmt_bp_vendor", indexes={@ORM\Index(name="nmt_bp_vendor_idx", columns={"created_by"}), @ORM\Index(name="nmt_bp_vendor_FK2_idx", columns={"country_id"})})
 * @ORM\Entity
 */
class NmtBpVendor
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
     * @ORM\Column(name="vendor_name", type="string", length=100, nullable=false)
     */
    private $vendorName;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_short_name", type="string", length=50, nullable=true)
     */
    private $vendorShortName;

    /**
     * @var string
     *
     * @ORM\Column(name="keywords", type="string", length=50, nullable=true)
     */
    private $keywords;

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
     * @ORM\Column(name="remarks", type="string", length=255, nullable=true)
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
     * @var \Application\Entity\NmtApplicationCountry
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\NmtApplicationCountry")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="country_id", referencedColumnName="id")
     * })
     */
    private $country;



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
     * Set vendorName
     *
     * @param string $vendorName
     *
     * @return NmtBpVendor
     */
    public function setVendorName($vendorName)
    {
        $this->vendorName = $vendorName;

        return $this;
    }

    /**
     * Get vendorName
     *
     * @return string
     */
    public function getVendorName()
    {
        return $this->vendorName;
    }

    /**
     * Set vendorShortName
     *
     * @param string $vendorShortName
     *
     * @return NmtBpVendor
     */
    public function setVendorShortName($vendorShortName)
    {
        $this->vendorShortName = $vendorShortName;

        return $this;
    }

    /**
     * Get vendorShortName
     *
     * @return string
     */
    public function getVendorShortName()
    {
        return $this->vendorShortName;
    }

    /**
     * Set keywords
     *
     * @param string $keywords
     *
     * @return NmtBpVendor
     */
    public function setKeywords($keywords)
    {
        $this->keywords = $keywords;

        return $this;
    }

    /**
     * Get keywords
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->keywords;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtBpVendor
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
     * @return NmtBpVendor
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
     * @return NmtBpVendor
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
     * @return NmtBpVendor
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
     * Set country
     *
     * @param \Application\Entity\NmtApplicationCountry $country
     *
     * @return NmtBpVendor
     */
    public function setCountry(\Application\Entity\NmtApplicationCountry $country = null)
    {
        $this->country = $country;

        return $this;
    }

    /**
     * Get country
     *
     * @return \Application\Entity\NmtApplicationCountry
     */
    public function getCountry()
    {
        return $this->country;
    }
}
<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtBpVendor
 *
 * @ORM\Table(name="nmt_bp_vendor", indexes={@ORM\Index(name="nmt_bp_vendor_idx", columns={"created_by"}), @ORM\Index(name="nmt_bp_vendor_FK2_idx", columns={"country_id"}), @ORM\Index(name="nmt_bp_vendor_FK3_idx", columns={"last_change_by"}), @ORM\Index(name="nmt_bp_vendor_FK4_idx", columns={"GL_account_id"})})
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
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="checksum", type="string", length=45, nullable=true)
     */
    private $checksum;

    /**
     * @var string
     *
     * @ORM\Column(name="vendor_number", type="string", length=15, nullable=true)
     */
    private $vendorNumber;

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
     * @ORM\Column(name="street", type="string", length=100, nullable=true)
     */
    private $street;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=45, nullable=true)
     */
    private $city;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=45, nullable=true)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=45, nullable=true)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="fax", type="string", length=45, nullable=true)
     */
    private $fax;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=45, nullable=true)
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\Column(name="contract_person", type="string", length=45, nullable=true)
     */
    private $contractPerson;

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
     * @var \Application\Entity\MlaUsers
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\MlaUsers")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="last_change_by", referencedColumnName="id")
     * })
     */
    private $lastChangeBy;

    /**
     * @var \Application\Entity\FinAccount
     *
     * @ORM\ManyToOne(targetEntity="Application\Entity\FinAccount")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="GL_account_id", referencedColumnName="id")
     * })
     */
    private $glAccount;



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
     * Set token
     *
     * @param string $token
     *
     * @return NmtBpVendor
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
     * Set checksum
     *
     * @param string $checksum
     *
     * @return NmtBpVendor
     */
    public function setChecksum($checksum)
    {
        $this->checksum = $checksum;

        return $this;
    }

    /**
     * Get checksum
     *
     * @return string
     */
    public function getChecksum()
    {
        return $this->checksum;
    }

    /**
     * Set vendorNumber
     *
     * @param string $vendorNumber
     *
     * @return NmtBpVendor
     */
    public function setVendorNumber($vendorNumber)
    {
        $this->vendorNumber = $vendorNumber;

        return $this;
    }

    /**
     * Get vendorNumber
     *
     * @return string
     */
    public function getVendorNumber()
    {
        return $this->vendorNumber;
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
     * Set lastChangeOn
     *
     * @param \DateTime $lastChangeOn
     *
     * @return NmtBpVendor
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
     * @return NmtBpVendor
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
     * Set street
     *
     * @param string $street
     *
     * @return NmtBpVendor
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set city
     *
     * @param string $city
     *
     * @return NmtBpVendor
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return NmtBpVendor
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return NmtBpVendor
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set fax
     *
     * @param string $fax
     *
     * @return NmtBpVendor
     */
    public function setFax($fax)
    {
        $this->fax = $fax;

        return $this;
    }

    /**
     * Get fax
     *
     * @return string
     */
    public function getFax()
    {
        return $this->fax;
    }

    /**
     * Set website
     *
     * @param string $website
     *
     * @return NmtBpVendor
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * Set contractPerson
     *
     * @param string $contractPerson
     *
     * @return NmtBpVendor
     */
    public function setContractPerson($contractPerson)
    {
        $this->contractPerson = $contractPerson;

        return $this;
    }

    /**
     * Get contractPerson
     *
     * @return string
     */
    public function getContractPerson()
    {
        return $this->contractPerson;
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

    /**
     * Set lastChangeBy
     *
     * @param \Application\Entity\MlaUsers $lastChangeBy
     *
     * @return NmtBpVendor
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

    /**
     * Set glAccount
     *
     * @param \Application\Entity\FinAccount $glAccount
     *
     * @return NmtBpVendor
     */
    public function setGlAccount(\Application\Entity\FinAccount $glAccount = null)
    {
        $this->glAccount = $glAccount;

        return $this;
    }

    /**
     * Get glAccount
     *
     * @return \Application\Entity\FinAccount
     */
    public function getGlAccount()
    {
        return $this->glAccount;
    }
}

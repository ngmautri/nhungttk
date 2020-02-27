<?php

namespace Application\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NmtApplicationCountry
 *
 * @ORM\Table(name="nmt_application_country", uniqueConstraints={@ORM\UniqueConstraint(name="nmt_application_country_country_name_UNIQUE", columns={"country_name"})}, indexes={@ORM\Index(name="nmt_application_country_idx", columns={"created_by"}), @ORM\Index(name="nmt_application_country_IDX1", columns={"token"})})
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
     * @ORM\Column(name="official_name_en", type="string", length=100, nullable=true)
     */
    private $officialNameEn;

    /**
     * @var string
     *
     * @ORM\Column(name="official_name_fr", type="string", length=100, nullable=true)
     */
    private $officialNameFr;

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
     * @var string
     *
     * @ORM\Column(name="country_numeric_code2", type="string", length=45, nullable=true)
     */
    private $countryNumericCode2;

    /**
     * @var string
     *
     * @ORM\Column(name="MARC", type="string", length=10, nullable=true)
     */
    private $marc;

    /**
     * @var string
     *
     * @ORM\Column(name="WMO", type="string", length=10, nullable=true)
     */
    private $wmo;

    /**
     * @var string
     *
     * @ORM\Column(name="DS", type="string", length=10, nullable=true)
     */
    private $ds;

    /**
     * @var string
     *
     * @ORM\Column(name="Dial", type="string", length=10, nullable=true)
     */
    private $dial;

    /**
     * @var string
     *
     * @ORM\Column(name="FIFA", type="string", length=10, nullable=true)
     */
    private $fifa;

    /**
     * @var string
     *
     * @ORM\Column(name="FIPS", type="string", length=10, nullable=true)
     */
    private $fips;

    /**
     * @var string
     *
     * @ORM\Column(name="GAUL", type="string", length=10, nullable=true)
     */
    private $gaul;

    /**
     * @var string
     *
     * @ORM\Column(name="IOC", type="string", length=10, nullable=true)
     */
    private $ioc;

    /**
     * @var string
     *
     * @ORM\Column(name="ISO4217_currency_alphabetic_code", type="string", length=10, nullable=true)
     */
    private $iso4217CurrencyAlphabeticCode;

    /**
     * @var string
     *
     * @ORM\Column(name="ISO4217_currency_country_name", type="string", length=100, nullable=true)
     */
    private $iso4217CurrencyCountryName;

    /**
     * @var string
     *
     * @ORM\Column(name="ISO4217_currency_minor_unit", type="string", length=100, nullable=true)
     */
    private $iso4217CurrencyMinorUnit;

    /**
     * @var string
     *
     * @ORM\Column(name="ISO4217_currency_name", type="string", length=10, nullable=true)
     */
    private $iso4217CurrencyName;

    /**
     * @var string
     *
     * @ORM\Column(name="ISO4217_currency_numeric_code", type="string", length=10, nullable=true)
     */
    private $iso4217CurrencyNumericCode;

    /**
     * @var string
     *
     * @ORM\Column(name="is_independent", type="string", length=100, nullable=true)
     */
    private $isIndependent;

    /**
     * @var string
     *
     * @ORM\Column(name="capital", type="string", length=45, nullable=true)
     */
    private $capital;

    /**
     * @var string
     *
     * @ORM\Column(name="continent", type="string", length=45, nullable=true)
     */
    private $continent;

    /**
     * @var string
     *
     * @ORM\Column(name="TLD", type="string", length=45, nullable=true)
     */
    private $tld;

    /**
     * @var string
     *
     * @ORM\Column(name="languages", type="string", length=45, nullable=true)
     */
    private $languages;

    /**
     * @var string
     *
     * @ORM\Column(name="geoname_id", type="string", length=45, nullable=true)
     */
    private $geonameId;

    /**
     * @var string
     *
     * @ORM\Column(name="EDGAR", type="string", length=45, nullable=true)
     */
    private $edgar;

    /**
     * @var boolean
     *
     * @ORM\Column(name="is_active", type="boolean", nullable=true)
     */
    private $isActive;

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
     * @var string
     *
     * @ORM\Column(name="token", type="string", length=45, nullable=true)
     */
    private $token;

    /**
     * @var string
     *
     * @ORM\Column(name="uuid", type="string", length=36, nullable=true)
     */
    private $uuid;

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
     * Set officialNameEn
     *
     * @param string $officialNameEn
     *
     * @return NmtApplicationCountry
     */
    public function setOfficialNameEn($officialNameEn)
    {
        $this->officialNameEn = $officialNameEn;

        return $this;
    }

    /**
     * Get officialNameEn
     *
     * @return string
     */
    public function getOfficialNameEn()
    {
        return $this->officialNameEn;
    }

    /**
     * Set officialNameFr
     *
     * @param string $officialNameFr
     *
     * @return NmtApplicationCountry
     */
    public function setOfficialNameFr($officialNameFr)
    {
        $this->officialNameFr = $officialNameFr;

        return $this;
    }

    /**
     * Get officialNameFr
     *
     * @return string
     */
    public function getOfficialNameFr()
    {
        return $this->officialNameFr;
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
     * Set countryNumericCode2
     *
     * @param string $countryNumericCode2
     *
     * @return NmtApplicationCountry
     */
    public function setCountryNumericCode2($countryNumericCode2)
    {
        $this->countryNumericCode2 = $countryNumericCode2;

        return $this;
    }

    /**
     * Get countryNumericCode2
     *
     * @return string
     */
    public function getCountryNumericCode2()
    {
        return $this->countryNumericCode2;
    }

    /**
     * Set marc
     *
     * @param string $marc
     *
     * @return NmtApplicationCountry
     */
    public function setMarc($marc)
    {
        $this->marc = $marc;

        return $this;
    }

    /**
     * Get marc
     *
     * @return string
     */
    public function getMarc()
    {
        return $this->marc;
    }

    /**
     * Set wmo
     *
     * @param string $wmo
     *
     * @return NmtApplicationCountry
     */
    public function setWmo($wmo)
    {
        $this->wmo = $wmo;

        return $this;
    }

    /**
     * Get wmo
     *
     * @return string
     */
    public function getWmo()
    {
        return $this->wmo;
    }

    /**
     * Set ds
     *
     * @param string $ds
     *
     * @return NmtApplicationCountry
     */
    public function setDs($ds)
    {
        $this->ds = $ds;

        return $this;
    }

    /**
     * Get ds
     *
     * @return string
     */
    public function getDs()
    {
        return $this->ds;
    }

    /**
     * Set dial
     *
     * @param string $dial
     *
     * @return NmtApplicationCountry
     */
    public function setDial($dial)
    {
        $this->dial = $dial;

        return $this;
    }

    /**
     * Get dial
     *
     * @return string
     */
    public function getDial()
    {
        return $this->dial;
    }

    /**
     * Set fifa
     *
     * @param string $fifa
     *
     * @return NmtApplicationCountry
     */
    public function setFifa($fifa)
    {
        $this->fifa = $fifa;

        return $this;
    }

    /**
     * Get fifa
     *
     * @return string
     */
    public function getFifa()
    {
        return $this->fifa;
    }

    /**
     * Set fips
     *
     * @param string $fips
     *
     * @return NmtApplicationCountry
     */
    public function setFips($fips)
    {
        $this->fips = $fips;

        return $this;
    }

    /**
     * Get fips
     *
     * @return string
     */
    public function getFips()
    {
        return $this->fips;
    }

    /**
     * Set gaul
     *
     * @param string $gaul
     *
     * @return NmtApplicationCountry
     */
    public function setGaul($gaul)
    {
        $this->gaul = $gaul;

        return $this;
    }

    /**
     * Get gaul
     *
     * @return string
     */
    public function getGaul()
    {
        return $this->gaul;
    }

    /**
     * Set ioc
     *
     * @param string $ioc
     *
     * @return NmtApplicationCountry
     */
    public function setIoc($ioc)
    {
        $this->ioc = $ioc;

        return $this;
    }

    /**
     * Get ioc
     *
     * @return string
     */
    public function getIoc()
    {
        return $this->ioc;
    }

    /**
     * Set iso4217CurrencyAlphabeticCode
     *
     * @param string $iso4217CurrencyAlphabeticCode
     *
     * @return NmtApplicationCountry
     */
    public function setIso4217CurrencyAlphabeticCode($iso4217CurrencyAlphabeticCode)
    {
        $this->iso4217CurrencyAlphabeticCode = $iso4217CurrencyAlphabeticCode;

        return $this;
    }

    /**
     * Get iso4217CurrencyAlphabeticCode
     *
     * @return string
     */
    public function getIso4217CurrencyAlphabeticCode()
    {
        return $this->iso4217CurrencyAlphabeticCode;
    }

    /**
     * Set iso4217CurrencyCountryName
     *
     * @param string $iso4217CurrencyCountryName
     *
     * @return NmtApplicationCountry
     */
    public function setIso4217CurrencyCountryName($iso4217CurrencyCountryName)
    {
        $this->iso4217CurrencyCountryName = $iso4217CurrencyCountryName;

        return $this;
    }

    /**
     * Get iso4217CurrencyCountryName
     *
     * @return string
     */
    public function getIso4217CurrencyCountryName()
    {
        return $this->iso4217CurrencyCountryName;
    }

    /**
     * Set iso4217CurrencyMinorUnit
     *
     * @param string $iso4217CurrencyMinorUnit
     *
     * @return NmtApplicationCountry
     */
    public function setIso4217CurrencyMinorUnit($iso4217CurrencyMinorUnit)
    {
        $this->iso4217CurrencyMinorUnit = $iso4217CurrencyMinorUnit;

        return $this;
    }

    /**
     * Get iso4217CurrencyMinorUnit
     *
     * @return string
     */
    public function getIso4217CurrencyMinorUnit()
    {
        return $this->iso4217CurrencyMinorUnit;
    }

    /**
     * Set iso4217CurrencyName
     *
     * @param string $iso4217CurrencyName
     *
     * @return NmtApplicationCountry
     */
    public function setIso4217CurrencyName($iso4217CurrencyName)
    {
        $this->iso4217CurrencyName = $iso4217CurrencyName;

        return $this;
    }

    /**
     * Get iso4217CurrencyName
     *
     * @return string
     */
    public function getIso4217CurrencyName()
    {
        return $this->iso4217CurrencyName;
    }

    /**
     * Set iso4217CurrencyNumericCode
     *
     * @param string $iso4217CurrencyNumericCode
     *
     * @return NmtApplicationCountry
     */
    public function setIso4217CurrencyNumericCode($iso4217CurrencyNumericCode)
    {
        $this->iso4217CurrencyNumericCode = $iso4217CurrencyNumericCode;

        return $this;
    }

    /**
     * Get iso4217CurrencyNumericCode
     *
     * @return string
     */
    public function getIso4217CurrencyNumericCode()
    {
        return $this->iso4217CurrencyNumericCode;
    }

    /**
     * Set isIndependent
     *
     * @param string $isIndependent
     *
     * @return NmtApplicationCountry
     */
    public function setIsIndependent($isIndependent)
    {
        $this->isIndependent = $isIndependent;

        return $this;
    }

    /**
     * Get isIndependent
     *
     * @return string
     */
    public function getIsIndependent()
    {
        return $this->isIndependent;
    }

    /**
     * Set capital
     *
     * @param string $capital
     *
     * @return NmtApplicationCountry
     */
    public function setCapital($capital)
    {
        $this->capital = $capital;

        return $this;
    }

    /**
     * Get capital
     *
     * @return string
     */
    public function getCapital()
    {
        return $this->capital;
    }

    /**
     * Set continent
     *
     * @param string $continent
     *
     * @return NmtApplicationCountry
     */
    public function setContinent($continent)
    {
        $this->continent = $continent;

        return $this;
    }

    /**
     * Get continent
     *
     * @return string
     */
    public function getContinent()
    {
        return $this->continent;
    }

    /**
     * Set tld
     *
     * @param string $tld
     *
     * @return NmtApplicationCountry
     */
    public function setTld($tld)
    {
        $this->tld = $tld;

        return $this;
    }

    /**
     * Get tld
     *
     * @return string
     */
    public function getTld()
    {
        return $this->tld;
    }

    /**
     * Set languages
     *
     * @param string $languages
     *
     * @return NmtApplicationCountry
     */
    public function setLanguages($languages)
    {
        $this->languages = $languages;

        return $this;
    }

    /**
     * Get languages
     *
     * @return string
     */
    public function getLanguages()
    {
        return $this->languages;
    }

    /**
     * Set geonameId
     *
     * @param string $geonameId
     *
     * @return NmtApplicationCountry
     */
    public function setGeonameId($geonameId)
    {
        $this->geonameId = $geonameId;

        return $this;
    }

    /**
     * Get geonameId
     *
     * @return string
     */
    public function getGeonameId()
    {
        return $this->geonameId;
    }

    /**
     * Set edgar
     *
     * @param string $edgar
     *
     * @return NmtApplicationCountry
     */
    public function setEdgar($edgar)
    {
        $this->edgar = $edgar;

        return $this;
    }

    /**
     * Get edgar
     *
     * @return string
     */
    public function getEdgar()
    {
        return $this->edgar;
    }

    /**
     * Set isActive
     *
     * @param boolean $isActive
     *
     * @return NmtApplicationCountry
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
     * Set token
     *
     * @param string $token
     *
     * @return NmtApplicationCountry
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
     * Set uuid
     *
     * @param string $uuid
     *
     * @return NmtApplicationCountry
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;

        return $this;
    }

    /**
     * Get uuid
     *
     * @return string
     */
    public function getUuid()
    {
        return $this->uuid;
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

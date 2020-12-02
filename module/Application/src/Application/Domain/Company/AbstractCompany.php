<?php
namespace Application\Domain\Company;

use Application\Domain\Shared\AggregateRoot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AbstractCompany extends AggregateRoot
{

    protected $id;

    protected $companyCode;

    protected $companyName;

    protected $defaultLogoId;

    protected $status;

    protected $createdOn;

    protected $isDefault;

    protected $token;

    protected $lastChangeOn;

    protected $revisionNo;

    protected $uuid;

    protected $defaultCurrency;

    protected $createdBy;

    protected $country;

    protected $defaultAddress;

    protected $lastChangeBy;

    protected $defaultWarehouse;

    protected $defaultLocale;

    protected $defaultLanguage;

    protected $defaultFormat;

    protected $defaultWarehouseCode;

    protected $defaultCurrencyIso;

    /**
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     *
     * @return mixed
     */
    public function getCompanyCode()
    {
        return $this->companyCode;
    }

    /**
     *
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     *
     * @return mixed
     */
    public function getDefaultLogoId()
    {
        return $this->defaultLogoId;
    }

    /**
     *
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     *
     * @return mixed
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     *
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     *
     * @return mixed
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     *
     * @return mixed
     */
    public function getRevisionNo()
    {
        return $this->revisionNo;
    }

    /**
     *
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     *
     * @return mixed
     */
    public function getDefaultCurrency()
    {
        return $this->defaultCurrency;
    }

    /**
     *
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     *
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     *
     * @return mixed
     */
    public function getDefaultAddress()
    {
        return $this->defaultAddress;
    }

    /**
     *
     * @return mixed
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    /**
     *
     * @return mixed
     */
    public function getDefaultWarehouse()
    {
        return $this->defaultWarehouse;
    }

    /**
     *
     * @return mixed
     */
    public function getDefaultLocale()
    {
        return $this->defaultLocale;
    }

    /**
     *
     * @return mixed
     */
    public function getDefaultLanguage()
    {
        return $this->defaultLanguage;
    }

    /**
     *
     * @return mixed
     */
    public function getDefaultFormat()
    {
        return $this->defaultFormat;
    }

    /**
     *
     * @return mixed
     */
    public function getDefaultWarehouseCode()
    {
        return $this->defaultWarehouseCode;
    }

    /**
     *
     * @return mixed
     */
    public function getDefaultCurrencyIso()
    {
        return $this->defaultCurrencyIso;
    }

    /**
     *
     * @param mixed $id
     */
    protected function setId($id)
    {
        $this->id = $id;
    }

    /**
     *
     * @param mixed $companyCode
     */
    protected function setCompanyCode($companyCode)
    {
        $this->companyCode = $companyCode;
    }

    /**
     *
     * @param mixed $companyName
     */
    protected function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
    }

    /**
     *
     * @param mixed $defaultLogoId
     */
    protected function setDefaultLogoId($defaultLogoId)
    {
        $this->defaultLogoId = $defaultLogoId;
    }

    /**
     *
     * @param mixed $status
     */
    protected function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     *
     * @param mixed $createdOn
     */
    protected function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
    }

    /**
     *
     * @param mixed $isDefault
     */
    protected function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
    }

    /**
     *
     * @param mixed $token
     */
    protected function setToken($token)
    {
        $this->token = $token;
    }

    /**
     *
     * @param mixed $lastChangeOn
     */
    protected function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
    }

    /**
     *
     * @param mixed $revisionNo
     */
    protected function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;
    }

    /**
     *
     * @param mixed $uuid
     */
    protected function setUuid($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
     *
     * @param mixed $defaultCurrency
     */
    protected function setDefaultCurrency($defaultCurrency)
    {
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     *
     * @param mixed $createdBy
     */
    protected function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
    }

    /**
     *
     * @param mixed $country
     */
    protected function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     *
     * @param mixed $defaultAddress
     */
    protected function setDefaultAddress($defaultAddress)
    {
        $this->defaultAddress = $defaultAddress;
    }

    /**
     *
     * @param mixed $lastChangeBy
     */
    protected function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
    }

    /**
     *
     * @param mixed $defaultWarehouse
     */
    protected function setDefaultWarehouse($defaultWarehouse)
    {
        $this->defaultWarehouse = $defaultWarehouse;
    }

    /**
     *
     * @param mixed $defaultLocale
     */
    protected function setDefaultLocale($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
    }

    /**
     *
     * @param mixed $defaultLanguage
     */
    protected function setDefaultLanguage($defaultLanguage)
    {
        $this->defaultLanguage = $defaultLanguage;
    }

    /**
     *
     * @param mixed $defaultFormat
     */
    protected function setDefaultFormat($defaultFormat)
    {
        $this->defaultFormat = $defaultFormat;
    }

    /**
     *
     * @param mixed $defaultWarehouseCode
     */
    protected function setDefaultWarehouseCode($defaultWarehouseCode)
    {
        $this->defaultWarehouseCode = $defaultWarehouseCode;
    }

    /**
     *
     * @param mixed $defaultCurrencyIso
     */
    protected function setDefaultCurrencyIso($defaultCurrencyIso)
    {
        $this->defaultCurrencyIso = $defaultCurrencyIso;
    }
}

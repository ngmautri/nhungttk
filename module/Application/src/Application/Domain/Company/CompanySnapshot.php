<?php
namespace Application\Domain\Company;

use Application\Domain\Shared\AbstractDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CompanySnapshot extends AbstractDTO
{

    public $id;

    public $companyCode;

    public $companyName;

    public $defaultLogoId;

    public $status;

    public $createdOn;

    public $isDefault;

    public $token;

    public $lastChangeOn;

    public $revisionNo;

    public $uuid;

    public $defaultLocale;

    public $defaultLanguage;

    public $defaultFormat;

    public $defaultWarehouseCode;

    public $defaultCurrencyIso;

    public $defaultCurrency;

    public $createdBy;

    public $country;

    public $defaultAddress;

    public $lastChangeBy;

    public $defaultWarehouse;

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
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
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
     * @param mixed $companyCode
     */
    public function setCompanyCode($companyCode)
    {
        $this->companyCode = $companyCode;
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
     * @param mixed $companyName
     */
    public function setCompanyName($companyName)
    {
        $this->companyName = $companyName;
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
     * @param mixed $defaultLogoId
     */
    public function setDefaultLogoId($defaultLogoId)
    {
        $this->defaultLogoId = $defaultLogoId;
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
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
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
     * @param mixed $createdOn
     */
    public function setCreatedOn($createdOn)
    {
        $this->createdOn = $createdOn;
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
     * @param mixed $isDefault
     */
    public function setIsDefault($isDefault)
    {
        $this->isDefault = $isDefault;
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
     * @param mixed $token
     */
    public function setToken($token)
    {
        $this->token = $token;
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
     * @param mixed $lastChangeOn
     */
    public function setLastChangeOn($lastChangeOn)
    {
        $this->lastChangeOn = $lastChangeOn;
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
     * @param mixed $revisionNo
     */
    public function setRevisionNo($revisionNo)
    {
        $this->revisionNo = $revisionNo;
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
     * @param mixed $uuid
     */
    public function setUuid($uuid)
    {
        $this->uuid = $uuid;
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
     * @param mixed $defaultLocale
     */
    public function setDefaultLocale($defaultLocale)
    {
        $this->defaultLocale = $defaultLocale;
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
     * @param mixed $defaultLanguage
     */
    public function setDefaultLanguage($defaultLanguage)
    {
        $this->defaultLanguage = $defaultLanguage;
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
     * @param mixed $defaultFormat
     */
    public function setDefaultFormat($defaultFormat)
    {
        $this->defaultFormat = $defaultFormat;
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
     * @param mixed $defaultWarehouseCode
     */
    public function setDefaultWarehouseCode($defaultWarehouseCode)
    {
        $this->defaultWarehouseCode = $defaultWarehouseCode;
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
     * @param mixed $defaultCurrencyIso
     */
    public function setDefaultCurrencyIso($defaultCurrencyIso)
    {
        $this->defaultCurrencyIso = $defaultCurrencyIso;
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
     * @param mixed $defaultCurrency
     */
    public function setDefaultCurrency($defaultCurrency)
    {
        $this->defaultCurrency = $defaultCurrency;
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
     * @param mixed $createdBy
     */
    public function setCreatedBy($createdBy)
    {
        $this->createdBy = $createdBy;
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
     * @param mixed $country
     */
    public function setCountry($country)
    {
        $this->country = $country;
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
     * @param mixed $defaultAddress
     */
    public function setDefaultAddress($defaultAddress)
    {
        $this->defaultAddress = $defaultAddress;
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
     * @param mixed $lastChangeBy
     */
    public function setLastChangeBy($lastChangeBy)
    {
        $this->lastChangeBy = $lastChangeBy;
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
     * @param mixed $defaultWarehouse
     */
    public function setDefaultWarehouse($defaultWarehouse)
    {
        $this->defaultWarehouse = $defaultWarehouse;
    }
}
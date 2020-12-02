<?php
namespace Application\Domain\Company;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class CompanyVO
{

    private $id;

    private $companyCode;

    private $companyName;

    private $defaultLogoId;

    private $status;

    private $createdOn;

    private $isDefault;

    private $token;

    private $lastChangeOn;

    private $revisionNo;

    private $uuid;

    private $defaultLocale;

    private $defaultLanguage;

    private $defaultFormat;

    private $defaultWarehouseCode;

    private $defaultCurrencyIso;

    private $defaultCurrency;

    private $createdBy;

    private $country;

    private $defaultAddress;

    private $lastChangeBy;

    private $defaultWarehouse;

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
}
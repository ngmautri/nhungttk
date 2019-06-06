<?php
namespace Application\Domain\Company;

use Application\Domain\Shared\AbstractEntity;
use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use Application\Application\DTO\Company\CompanyDTOAssembler;
use Application\Notification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AbstractCompany extends AbstractEntity
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

    // ============
    /**
     *
     * @var AbstractSpecificationFactory
     */
    protected $sharedSpecificationFactory;

    /**
     *
     * @return boolean
     */
    public function isValid()
    {
        /**
         *
         * @var Notification $notification
         */
        $notification = $this->validate();

        if ($notification == null)
            return false;

        return ! $notification->hasErrors();
    }

    /**
     *
     * @param Notification $notification
     * @return string|\Application\Notification
     */
    public function validate($notification = null)
    {
        if ($notification == null)
            $notification = new Notification();

        if ($this->sharedSpecificationFactory == null) {
            $notification->addError("Validators is not found");
            return $notification;
        }

        if ($this->sharedSpecificationFactory->getNullorBlankSpecification()->isSatisfiedBy($this->defaultCurrency)) {
            $notification->addError("Default currency is empty");
        } else {
            $spec = $this->sharedSpecificationFactory->getCurrencyExitsSpecification();
            if (! $spec->isSatisfiedBy($this->defaultCurrency))
                $notification->addError("Default currency not exits..." . $this->defaultCurrency);
        }

        if ($this->sharedSpecificationFactory->getNullorBlankSpecification()->isSatisfiedBy($this->defaultWarehouse)) {
            $notification->addError("Default warehouse is empty");
        } else {
            $spec = $this->sharedSpecificationFactory->getWarehouseExitsSpecification();
            if (! $spec->isSatisfiedBy($this->defaultWarehouse))
                $notification->addError("Default warehouse not exits..." . $this->defaultWarehouse);
        }
        return $notification;
    }

    /**
     *
     * @return NULL|\Application\Application\DTO\Company\CompanyDTO
     */
    public function makeDTO()
    {
        return CompanyDTOAssembler::createDTOFrom($this);
    }

    /**
     *
     * @return NULL|\Application\Domain\Company\CompanySnapshot
     */
    public function makeSnapshot()
    {
        return CompanySnapshotAssembler::createSnapshotFrom($this);
    }

    /**
     *
     * @param CompanySnapshot $snapshot
     */
    public function makeFromSnapshot($snapshot)
    {
        if (! $snapshot instanceof CompanySnapshot)
            return;

        $this->id = $snapshot->id;
        $this->companyCode = $snapshot->companyCode;
        $this->companyName = $snapshot->companyName;
        $this->defaultLogoId = $snapshot->defaultLogoId;
        $this->status = $snapshot->status;
        $this->createdOn = $snapshot->createdOn;
        $this->isDefault = $snapshot->isDefault;
        $this->token = $snapshot->token;
        $this->lastChangeOn = $snapshot->lastChangeOn;
        $this->revisionNo = $snapshot->revisionNo;
        $this->uuid = $snapshot->uuid;
        $this->defaultCurrency = $snapshot->defaultCurrency;
        $this->createdBy = $snapshot->createdBy;
        $this->country = $snapshot->country;
        $this->defaultAddress = $snapshot->defaultAddress;
        $this->lastChangeBy = $snapshot->lastChangeBy;
        $this->defaultWarehouse = $snapshot->defaultWarehouse;
    }

    /**
     *
     * @return \Application\Domain\Shared\Specification\AbstractSpecificationFactory
     */
    public function getSharedSpecificationFactory()
    {
        return $this->sharedSpecificationFactory;
    }

    /**
     *
     * @param \Application\Domain\Shared\Specification\AbstractSpecificationFactory $sharedSpecificationFactory
     */
    public function setSharedSpecificationFactory($sharedSpecificationFactory)
    {
        $this->sharedSpecificationFactory = $sharedSpecificationFactory;
    }
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getCompanyCode()
    {
        return $this->companyCode;
    }

    /**
     * @return mixed
     */
    public function getCompanyName()
    {
        return $this->companyName;
    }

    /**
     * @return mixed
     */
    public function getDefaultLogoId()
    {
        return $this->defaultLogoId;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     * @return mixed
     */
    public function getIsDefault()
    {
        return $this->isDefault;
    }

    /**
     * @return mixed
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return mixed
     */
    public function getLastChangeOn()
    {
        return $this->lastChangeOn;
    }

    /**
     * @return mixed
     */
    public function getRevisionNo()
    {
        return $this->revisionNo;
    }

    /**
     * @return mixed
     */
    public function getUuid()
    {
        return $this->uuid;
    }

    /**
     * @return mixed
     */
    public function getDefaultCurrency()
    {
        return $this->defaultCurrency;
    }

    /**
     * @return mixed
     */
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     * @return mixed
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * @return mixed
     */
    public function getDefaultAddress()
    {
        return $this->defaultAddress;
    }

    /**
     * @return mixed
     */
    public function getLastChangeBy()
    {
        return $this->lastChangeBy;
    }

    /**
     * @return mixed
     */
    public function getDefaultWarehouse()
    {
        return $this->defaultWarehouse;
    }


}

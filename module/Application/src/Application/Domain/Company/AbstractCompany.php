<?php
namespace Application\Domain\Company;

use Application\Application\DTO\Company\CompanyDTO;
use Application\Domain\Shared\AggregateRoot;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;

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

    /**
     *
     * @return NULL|object
     */
    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new CompanySnapshot());
    }

    /**
     *
     * @return NULL|object
     */
    public function makeDetailsSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new CompanyDetailsSnapshot());
    }

    public function makeDTO()
    {
        return DTOFactory::createDTOFrom($this, new CompanyDTO());
    }

    /**
     * 
     * @param CompanyDetailsSnapshot $snapshot
     */
    public function makeFromDetailsSnapshot(CompanyDetailsSnapshot $snapshot)
    {
        if (! $snapshot instanceof CompanyDetailsSnapshot)
            return;

        SnapshotAssembler::makeFromSnapshot($this, $snapshot);
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

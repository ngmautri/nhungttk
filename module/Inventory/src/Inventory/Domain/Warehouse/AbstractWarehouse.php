<?php
namespace Inventory\Domain\Warehouse;

use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use Inventory\Application\DTO\Warehouse\WarehouseDTOAssembler;
use Inventory\Domain\Warehouse\WarehouseSnapshot;
use Inventory\Domain\Warehouse\WarehouseSnapshotAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractWarehouse
{

    protected $id;

    protected $whCode;

    protected $whName;

    protected $whAddress;

    protected $whContactPerson;

    protected $whTelephone;

    protected $whEmail;

    protected $isLocked;

    protected $whStatus;

    protected $remarks;

    protected $isDefault;

    protected $createdOn;

    protected $sysNumber;

    protected $token;

    protected $lastChangeOn;

    protected $revisionNo;

    protected $createdBy;

    protected $company;

    protected $whCountry;

    protected $lastChangeBy;

    protected $stockkeeper;

    protected $whController;

    protected $location;

    protected $uuid;
    
    /**
     *
     * @var AbstractSpecificationFactory $sharedSpecificationFactory;
     */
    protected $sharedSpecificationFactory;
    
    /**
     *
     * @var WarehouseCmdRepositoryInterface $cmdRepository;
     */
    protected $cmdRepository;
    
    /**
     *
     * @var WarehouseQueryRepositoryInterface $queryRepository;
     */
    protected $queryRepository;
    
    /**
     *
     * @return \Inventory\Domain\Warehouse\WarehouseCmdRepositoryInterface
     */
    public function getCmdRepository()
    {
        return $this->cmdRepository;
    }
    
    /**
     *
     * @return \Inventory\Domain\Warehouse\WarehouseQueryRepositoryInterface
     */
    public function getQueryRepository()
    {
        return $this->queryRepository;
    }
    
    /**
     *
     * @param WarehouseCmdRepositoryInterface $cmdRepository ;
     */
    public function setCmdRepository(WarehouseCmdRepositoryInterface $cmdRepository)
    {
        $this->cmdRepository = $cmdRepository;
    }
    
    /**
     * @param mixed $queryRepository
     */
    public function setQueryRepository(WarehouseQueryRepositoryInterface $queryRepository)
    {
        $this->queryRepository = $queryRepository;
    }

    /**
     *
     * @return NULL|\Inventory\Domain\Warehouse\Transaction\TransactionSnapshot
     */
    public function makeSnapshot()
    {
        return WarehouseSnapshotAssembler::createSnapshotFrom($this);
    }

    public function makeDTO()
    {
        return WarehouseDTOAssembler::createDTOFrom($this);
    }

    /**
     *
     * @param WarehouseSnapshot $snapshot
     */
    public function makeFromSnapshot($snapshot)
    {
        if (! $snapshot instanceof WarehouseSnapshot)
            return;

        $this->id = $snapshot->id;
        $this->whCode = $snapshot->whCode;
        $this->whName = $snapshot->whName;
        $this->whAddress = $snapshot->whAddress;
        $this->whContactPerson = $snapshot->whContactPerson;
        $this->whTelephone = $snapshot->whTelephone;
        $this->whEmail = $snapshot->whEmail;
        $this->isLocked = $snapshot->isLocked;
        $this->whStatus = $snapshot->whStatus;
        $this->remarks = $snapshot->remarks;
        $this->isDefault = $snapshot->isDefault;
        $this->createdOn = $snapshot->createdOn;
        $this->sysNumber = $snapshot->sysNumber;
        $this->token = $snapshot->token;
        $this->lastChangeOn = $snapshot->lastChangeOn;
        $this->revisionNo = $snapshot->revisionNo;
        $this->createdBy = $snapshot->createdBy;
        $this->company = $snapshot->company;
        $this->whCountry = $snapshot->whCountry;
        $this->lastChangeBy = $snapshot->lastChangeBy;
        $this->stockkeeper = $snapshot->stockkeeper;
        $this->whController = $snapshot->whController;
        $this->location = $snapshot->location;
        $this->uuid = $snapshot->uuid;
    }

    /**
     *
     * @return WarehouseDTO;
     */
    public function createDTO()
    {
        $dto = WarehouseDTOAssembler::createDTOFrom($this);
        return $dto;
    }

    /**
     *
     * @return WarehouseSnapshot;
     */
    public function createSnapshot()
    {
        $snapshot = WarehouseSnapshotAssembler::createSnapshotFrom($this);
        return $snapshot;
    }

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
    public function getWhCode()
    {
        return $this->whCode;
    }

    /**
     *
     * @return mixed
     */
    public function getWhName()
    {
        return $this->whName;
    }

    /**
     *
     * @return mixed
     */
    public function getWhAddress()
    {
        return $this->whAddress;
    }

    /**
     *
     * @return mixed
     */
    public function getWhContactPerson()
    {
        return $this->whContactPerson;
    }

    /**
     *
     * @return mixed
     */
    public function getWhTelephone()
    {
        return $this->whTelephone;
    }

    /**
     *
     * @return mixed
     */
    public function getWhEmail()
    {
        return $this->whEmail;
    }

    /**
     *
     * @return mixed
     */
    public function getIsLocked()
    {
        return $this->isLocked;
    }

    /**
     *
     * @return mixed
     */
    public function getWhStatus()
    {
        return $this->whStatus;
    }

    /**
     *
     * @return mixed
     */
    public function getRemarks()
    {
        return $this->remarks;
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
    public function getCreatedOn()
    {
        return $this->createdOn;
    }

    /**
     *
     * @return mixed
     */
    public function getSysNumber()
    {
        return $this->sysNumber;
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
    public function getCreatedBy()
    {
        return $this->createdBy;
    }

    /**
     *
     * @return mixed
     */
    public function getCompany()
    {
        return $this->company;
    }

    /**
     *
     * @return mixed
     */
    public function getWhCountry()
    {
        return $this->whCountry;
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
    public function getStockkeeper()
    {
        return $this->stockkeeper;
    }

    /**
     *
     * @return mixed
     */
    public function getWhController()
    {
        return $this->whController;
    }

    /**
     *
     * @return mixed
     */
    public function getLocation()
    {
        return $this->location;
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
    
}
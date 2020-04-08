<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Procure\Application\DTO\Po\PORowDTO;
use Procure\Application\DTO\Po\PORowDetailsDTO;
use Procure\Domain\GenericDoc;
use Procure\Domain\GenericRow;

/**
 * PO Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PORow extends GenericRow
{
    
    private static $instance = null;
    
    
    // Specific Attributes
    //=================================
       
    protected $draftGrQuantity;
    
    protected $postedGrQuantity;
    
    protected $confirmedGrBalance;
    
    protected $openGrBalance;
    
    protected $draftAPQuantity;
    
    protected $postedAPQuantity;
    
    protected $openAPQuantity;
    
    protected $billedAmount;
    
    protected $openAPAmount;

  
    private function __construct()
    {}

    /**
     *
     * @return \Procure\Domain\PurchaseOrder\PORow
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new PORow();
        }
        return self::$instance;
    }

    /**
     * Create properities
     */
    public static function createSnapshotProps()
    {
        $entity = new self();
        $reflectionClass = new \ReflectionClass($entity);
        
        $props = $reflectionClass->getProperties();
        
        foreach ($props as $property) {
            
            if ($property->class == $reflectionClass->getName()) {
                $property->setAccessible(true);
                $propertyName = $property->getName();
                print "\n" . "public $" . $propertyName . ";";
            }
        }
    }
    
    public static function createAllSnapshotProps()
    {
        $entity = new self();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "public $" . $propertyName . ";";
        }
    }

    /**
     * this should be called when posted.
     *
     * @return \Procure\Domain\PurchaseOrder\PORow
     */
    public function setAsPosted($postedBy, $postedDate)
    {
        $this->isPosted = 1;
        $this->isDraft = 0;
        $this->docStatus = PODocStatus::DOC_STATUS_POSTED;
        $this->lastchangeOn = (date_format($postedDate, 'Y-m-d H:i:s'));
        $this->lastchangeBy = $postedBy;
    }

    /**
     *
     * @return NULL|PORowSnapshot
     */
    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new PORowSnapshot());
    }

    /**
     *
     * @return NULL|\Procure\Application\DTO\Po\PORowDTO
     */
    public function makeDTO()
    {
        return \Application\Domain\Shared\DTOFactory::createDTOFrom($this, new PORowDTO());
    }

    /**
     *
     * @return NULL|\Procure\Application\DTO\Po\PORowDTO
     */
    public function makeDetailsDTO()
    {
        $dto = new PORowDetailsDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @return NULL|object
     */
    public function makeDTOForGrid()
    {
        $dto = new PORowDetailsDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @param PORowSnapshot $snapshot
     */
    public static function makeFromSnapshot(PORowSnapshot $snapshot)
    {
        if (! $snapshot instanceof PORowSnapshot) {
            return null;
        }

        $instance = new self();

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * @param PORowDetailsSnapshot $snapshot
     */
    public static function makeFromDetailsSnapshot(PORowDetailsSnapshot $snapshot)
    {
        if (! $snapshot instanceof PORowDetailsSnapshot)
            return;

        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }
    /**
     * @param mixed $draftGrQuantity
     */
    protected function setDraftGrQuantity($draftGrQuantity)
    {
        $this->draftGrQuantity = $draftGrQuantity;
    }

    /**
     * @param mixed $postedGrQuantity
     */
    protected function setPostedGrQuantity($postedGrQuantity)
    {
        $this->postedGrQuantity = $postedGrQuantity;
    }

    /**
     * @param mixed $confirmedGrBalance
     */
    protected function setConfirmedGrBalance($confirmedGrBalance)
    {
        $this->confirmedGrBalance = $confirmedGrBalance;
    }

    /**
     * @param mixed $openGrBalance
     */
    protected function setOpenGrBalance($openGrBalance)
    {
        $this->openGrBalance = $openGrBalance;
    }

    /**
     * @param mixed $draftAPQuantity
     */
    protected function setDraftAPQuantity($draftAPQuantity)
    {
        $this->draftAPQuantity = $draftAPQuantity;
    }

    /**
     * @param mixed $postedAPQuantity
     */
    protected function setPostedAPQuantity($postedAPQuantity)
    {
        $this->postedAPQuantity = $postedAPQuantity;
    }

    /**
     * @param mixed $openAPQuantity
     */
    protected function setOpenAPQuantity($openAPQuantity)
    {
        $this->openAPQuantity = $openAPQuantity;
    }

    /**
     * @param mixed $billedAmount
     */
    protected function setBilledAmount($billedAmount)
    {
        $this->billedAmount = $billedAmount;
    }

    /**
     * @param mixed $openAPAmount
     */
    protected function setOpenAPAmount($openAPAmount)
    {
        $this->openAPAmount = $openAPAmount;
    }
    /**
     * @return mixed
     */
    public function getDraftGrQuantity()
    {
        return $this->draftGrQuantity;
    }

    /**
     * @return mixed
     */
    public function getPostedGrQuantity()
    {
        return $this->postedGrQuantity;
    }

    /**
     * @return mixed
     */
    public function getConfirmedGrBalance()
    {
        return $this->confirmedGrBalance;
    }

    /**
     * @return mixed
     */
    public function getOpenGrBalance()
    {
        return $this->openGrBalance;
    }

    /**
     * @return mixed
     */
    public function getDraftAPQuantity()
    {
        return $this->draftAPQuantity;
    }

    /**
     * @return mixed
     */
    public function getPostedAPQuantity()
    {
        return $this->postedAPQuantity;
    }

    /**
     * @return mixed
     */
    public function getOpenAPQuantity()
    {
        return $this->openAPQuantity;
    }

    /**
     * @return mixed
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     * @return mixed
     */
    public function getOpenAPAmount()
    {
        return $this->openAPAmount;
    }


}

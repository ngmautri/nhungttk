<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Application\DTO\Po\PORowDTO;
use Procure\Application\DTO\Po\PORowDetailsDTO;
use Procure\Domain\GenericRow;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\QuotationRequest\QRRow;
use Procure\Domain\Shared\Constants;

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
    // =================================
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

    public static function createFromQuoteRow(QRRow $sourceObj, CommandOptions $options)
    {
        if (! $sourceObj instanceof QRRow) {
            throw new InvalidArgumentException("Quotation document is required!");
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var PORow $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        $instance->setDocType(Constants::PROCURE_DOC_TYPE_PO_FROM_QOUTE); // important.
                                                                          // $instance->setQ($sourceObj->getId()); // Important

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $instance->initRow($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        return $instance;
    }

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
     *
     * @param mixed $draftGrQuantity
     */
    protected function setDraftGrQuantity($draftGrQuantity)
    {
        $this->draftGrQuantity = $draftGrQuantity;
    }

    /**
     *
     * @param mixed $postedGrQuantity
     */
    protected function setPostedGrQuantity($postedGrQuantity)
    {
        $this->postedGrQuantity = $postedGrQuantity;
    }

    /**
     *
     * @param mixed $confirmedGrBalance
     */
    protected function setConfirmedGrBalance($confirmedGrBalance)
    {
        $this->confirmedGrBalance = $confirmedGrBalance;
    }

    /**
     *
     * @param mixed $openGrBalance
     */
    protected function setOpenGrBalance($openGrBalance)
    {
        $this->openGrBalance = $openGrBalance;
    }

    /**
     *
     * @param mixed $draftAPQuantity
     */
    protected function setDraftAPQuantity($draftAPQuantity)
    {
        $this->draftAPQuantity = $draftAPQuantity;
    }

    /**
     *
     * @param mixed $postedAPQuantity
     */
    protected function setPostedAPQuantity($postedAPQuantity)
    {
        $this->postedAPQuantity = $postedAPQuantity;
    }

    /**
     *
     * @param mixed $openAPQuantity
     */
    protected function setOpenAPQuantity($openAPQuantity)
    {
        $this->openAPQuantity = $openAPQuantity;
    }

    /**
     *
     * @param mixed $billedAmount
     */
    protected function setBilledAmount($billedAmount)
    {
        $this->billedAmount = $billedAmount;
    }

    /**
     *
     * @param mixed $openAPAmount
     */
    protected function setOpenAPAmount($openAPAmount)
    {
        $this->openAPAmount = $openAPAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftGrQuantity()
    {
        return $this->draftGrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedGrQuantity()
    {
        return $this->postedGrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getConfirmedGrBalance()
    {
        return $this->confirmedGrBalance;
    }

    /**
     *
     * @return mixed
     */
    public function getOpenGrBalance()
    {
        return $this->openGrBalance;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftAPQuantity()
    {
        return $this->draftAPQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedAPQuantity()
    {
        return $this->postedAPQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getOpenAPQuantity()
    {
        return $this->openAPQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getBilledAmount()
    {
        return $this->billedAmount;
    }

    /**
     *
     * @return mixed
     */
    public function getOpenAPAmount()
    {
        return $this->openAPAmount;
    }
}

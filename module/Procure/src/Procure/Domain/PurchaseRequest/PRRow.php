<?php
namespace Procure\Domain\PurchaseRequest;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Application\DTO\Pr\PrRowDTO;
use Procure\Domain\GenericDoc;
use Webmozart\Assert\Assert;
use DateTime;

/**
 * PR Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PRRow extends BaseRow
{

    // Value Object
    private $prId;

    private $prQuantity;

    private static $instance = null;

    // Adddtional Attributes.
    // ====================
    protected $draftPoQuantity;

    protected $postedPoQuantity;

    protected $draftGrQuantity;

    protected $postedGrQuantity;

    protected $draftApQuantity;

    protected $postedApQuantity;

    protected $draftStockQrQuantity;

    protected $postedStockQrQuantity;

    protected $lastVendorId;

    protected $lastVendorName;

    protected $lastUnitPrice;

    protected $lastCurrency;

    protected function createVO(GenericDoc $rootDoc)
    {
        $this->createUomVO();
        $this->createQuantityVO();
    }

    public static function createFromSnapshot(PRDoc $rootDoc, PRRowSnapshot $snapshot)
    {
        Assert::isInstanceOf($rootDoc, PRDoc::class, "PR is required!");
        Assert::isInstanceOf($snapshot, PRRowSnapshot::class, "PR row snapshot is required!");

        $instance = new self();

        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);
        $instance->createVO($rootDoc);
        return $instance;
    }

    /**
     *
     * @param PRDoc $rootDoc
     * @param PrRow $sourceObj
     * @param CommandOptions $options
     * @return \Procure\Domain\PurchaseRequest\PrRow
     */
    public static function cloneFrom(PRDoc $rootDoc, PrRow $sourceObj, CommandOptions $options)
    {
        Assert::isInstanceOf($rootDoc, PRDoc::class, "PR is required!");
        Assert::isInstanceOf($sourceObj, PrRow::class, "PR row is required!");
        Assert::notNull($options, "No Options is found");

        /**
         *
         * @var PrRow $instance
         */
        $instance = new self();

        $exculdedProps = [
            'rowIdentifer',
            'prId',
            'pr',
            'prQuantity',
            'edt',
            'id',
            'token',
            'uuid'
        ];

        $instance = $sourceObj->convertExcludeFieldsTo($instance, $exculdedProps);

        $instance->initRow($options);
        $today = new DateTime();
        $instance->edt = $today->modify("10 days")->format("Y-m-d");

        $instance->standardConvertFactor = $instance->conversionFactor;

        if ($instance->standardConvertFactor == null) {
            $instance->standardConvertFactor = 1;
        }
        return $instance;
    }

    /**
     *
     * @return mixed
     */
    public function getLastVendorId()
    {
        return $this->lastVendorId;
    }

    /**
     *
     * @return mixed
     */
    public function getLastVendorName()
    {
        return $this->lastVendorName;
    }

    /**
     *
     * @return mixed
     */
    public function getLastUnitPrice()
    {
        return $this->lastUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getLastCurrency()
    {
        return $this->lastCurrency;
    }

    /**
     *
     * @param mixed $lastVendorId
     */
    public function setLastVendorId($lastVendorId)
    {
        $this->lastVendorId = $lastVendorId;
    }

    /**
     *
     * @param mixed $lastVendorName
     */
    public function setLastVendorName($lastVendorName)
    {
        $this->lastVendorName = $lastVendorName;
    }

    /**
     *
     * @param mixed $lastUnitPrice
     */
    public function setLastUnitPrice($lastUnitPrice)
    {
        $this->lastUnitPrice = $lastUnitPrice;
    }

    /**
     *
     * @param mixed $lastCurrency
     */
    public function setLastCurrency($lastCurrency)
    {
        $this->lastCurrency = $lastCurrency;
    }

    // ===================
    private function __construct()
    {}

    /**
     *
     * @param PRRowSnapshot $snapshot
     * @return NULL|\Procure\Domain\AccountPayable\APRow
     */
    public static function makeFromSnapshot(PRRowSnapshot $snapshot)
    {
        if (! $snapshot instanceof PRRowSnapshot) {
            return null;
        }

        $instance = new self();

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericRow::makeSnapshot()
     */
    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new PRRowSnapshot());
    }

    /**
     *
     * @return NULL|object
     */
    public function makeDetailsDTO()
    {
        $dto = new PrRowDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     *
     *
     * /**
     *
     * @return \Procure\Domain\AccountPayable\APRow
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new PRRow();
        }
        return self::$instance;
    }

    /**
     *
     * @return \Procure\Domain\AccountPayable\APRow
     */
    public static function createInstance()
    {
        return new PRRow();
    }

    /**
     *
     * @return mixed
     */
    public function getDraftPoQuantity()
    {
        return $this->draftPoQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedPoQuantity()
    {
        return $this->postedPoQuantity;
    }

    /**
     *
     * @param \Procure\Domain\PurchaseRequest\PRRow $instance
     */
    protected static function setInstance($instance)
    {
        PRRow::$instance = $instance;
    }

    /**
     *
     * @param mixed $draftPoQuantity
     */
    protected function setDraftPoQuantity($draftPoQuantity)
    {
        $this->draftPoQuantity = $draftPoQuantity;
    }

    /**
     *
     * @param mixed $postedPoQuantity
     */
    protected function setPostedPoQuantity($postedPoQuantity)
    {
        $this->postedPoQuantity = $postedPoQuantity;
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
    public function getDraftApQuantity()
    {
        return $this->draftApQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedApQuantity()
    {
        return $this->postedApQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getDraftStockQrQuantity()
    {
        return $this->draftStockQrQuantity;
    }

    /**
     *
     * @return mixed
     */
    public function getPostedStockQrQuantity()
    {
        return $this->postedStockQrQuantity;
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
     * @param mixed $draftApQuantity
     */
    protected function setDraftApQuantity($draftApQuantity)
    {
        $this->draftApQuantity = $draftApQuantity;
    }

    /**
     *
     * @param mixed $postedApQuantity
     */
    protected function setPostedApQuantity($postedApQuantity)
    {
        $this->postedApQuantity = $postedApQuantity;
    }

    /**
     *
     * @param mixed $draftStockQrQuantity
     */
    protected function setDraftStockQrQuantity($draftStockQrQuantity)
    {
        $this->draftStockQrQuantity = $draftStockQrQuantity;
    }

    /**
     *
     * @param mixed $postedStockQrQuantity
     */
    protected function setPostedStockQrQuantity($postedStockQrQuantity)
    {
        $this->postedStockQrQuantity = $postedStockQrQuantity;
    }
}

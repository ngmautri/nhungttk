<?php
namespace Procure\Domain\PurchaseRequest;

use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\GenericDoc;
use Procure\Domain\Contracts\ProcureTrxStatus;
use Webmozart\Assert\Assert;
use DateTime;

/**
 * PR Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRRow extends BasePrRow
{

    private static $instance = null;

    /*
     * |=============================
     * | Addtional Field
     * |
     * |=============================
     */
    private $prId;

    private $prQuantity;

    // Last Purchase
    protected $lastVendorId;

    protected $lastVendorName;

    protected $lastUnitPrice;

    protected $lastStandardUnitPrice;

    protected $lastStandardConvertFactor;

    protected $lastCurrency;

    /*
     * |=============================
     * | Methods
     * |
     * |=============================
     */
    public function updateRowStatus()
    {
        $this->createVO(); // important

        if ($this->getPostedStandardQoQuantity() > 0) {
            $this->setTransactionStatus(ProcureTrxStatus::HAS_QUOTATION);
        }

        if ($this->getPostedStandardPoQuantity() > 0) {
            $this->setTransactionStatus(ProcureTrxStatus::COMMITTED);

            if ($this->getConvertedStandardQuantity() - $this->getPostedStandardPoQuantity() > 0) {
                $this->setTransactionStatus(ProcureTrxStatus::PARTIAL_COMMITTED);
            }
        }

        if ($this->getPostedStandardGrQuantity() > 0) {
            $this->setTransactionStatus(ProcureTrxStatus::COMPLETED);

            if ($this->getConvertedStandardQuantity() - $this->getPostedStandardGrQuantity() > 0) {
                $this->setTransactionStatus(ProcureTrxStatus::PARTIAL_COMPLETED);
            }
        }
    }

    protected function createVO(GenericDoc $rootDoc = null)
    {
        if ($this->getCreatedVO()) {
            return;
        }

        $this->createUomVO();
        $this->createQuantityVO();
        $this->setCreatedVO(TRUE);
    }

    /**
     *
     * @param PRDoc $rootDoc
     * @param PRRowSnapshot $snapshot
     * @return \Procure\Domain\PurchaseRequest\PRRow
     */
    public static function createFromSnapshot(PRDoc $rootDoc, PRRowSnapshot $snapshot)
    {
        Assert::isInstanceOf($rootDoc, PRDoc::class, "PR is required!");
        Assert::isInstanceOf($snapshot, PRRowSnapshot::class, "PR row snapshot is required!");

        $instance = new self();

        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);
        $instance->createVO($rootDoc); // important
        return $instance;
    }

    public static function constructFromDB(PRRowSnapshot $snapshot)
    {
        if (! $snapshot instanceof PRRowSnapshot) {
            return null;
        }

        $instance = new self();

        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);
        $instance->setConstructedFromDB(TRUE);

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

    public static function makeFromSnapshot(PRRowSnapshot $snapshot)
    {
        if (! $snapshot instanceof PRRowSnapshot) {
            return null;
        }

        $instance = new self();

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /*
     * |=============================
     * | Getter Setter.
     * |
     * |=============================
     */

    /**
     *
     * @param mixed $prId
     */
    protected function setPrId($prId)
    {
        $this->prId = $prId;
    }

    /**
     *
     * @param mixed $prQuantity
     */
    protected function setPrQuantity($prQuantity)
    {
        $this->prQuantity = $prQuantity;
    }

    /**
     *
     * @param mixed $lastVendorId
     */
    protected function setLastVendorId($lastVendorId)
    {
        $this->lastVendorId = $lastVendorId;
    }

    /**
     *
     * @param mixed $lastVendorName
     */
    protected function setLastVendorName($lastVendorName)
    {
        $this->lastVendorName = $lastVendorName;
    }

    /**
     *
     * @param mixed $lastUnitPrice
     */
    protected function setLastUnitPrice($lastUnitPrice)
    {
        $this->lastUnitPrice = $lastUnitPrice;
    }

    /**
     *
     * @param mixed $lastStandardUnitPrice
     */
    protected function setLastStandardUnitPrice($lastStandardUnitPrice)
    {
        $this->lastStandardUnitPrice = $lastStandardUnitPrice;
    }

    /**
     *
     * @param mixed $lastStandardConvertFactor
     */
    protected function setLastStandardConvertFactor($lastStandardConvertFactor)
    {
        $this->lastStandardConvertFactor = $lastStandardConvertFactor;
    }

    /**
     *
     * @param mixed $lastCurrency
     */
    protected function setLastCurrency($lastCurrency)
    {
        $this->lastCurrency = $lastCurrency;
    }

    /**
     *
     * @return mixed
     */
    public static function getInstance()
    {
        return PRRow::$instance;
    }

    /**
     *
     * @return mixed
     */
    public function getPrId()
    {
        return $this->prId;
    }

    /**
     *
     * @return mixed
     */
    public function getPrQuantity()
    {
        return $this->prQuantity;
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
    public function getLastStandardUnitPrice()
    {
        return $this->lastStandardUnitPrice;
    }

    /**
     *
     * @return mixed
     */
    public function getLastStandardConvertFactor()
    {
        return $this->lastStandardConvertFactor;
    }

    /**
     *
     * @return mixed
     */
    public function getLastCurrency()
    {
        return $this->lastCurrency;
    }
}

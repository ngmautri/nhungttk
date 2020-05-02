<?php
namespace Procure\Domain\GoodsReceipt;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Application\DTO\Gr\GrRowDTO;
use Procure\Domain\GenericRow;
use Procure\Domain\AccountPayable\APRow;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Domain\Shared\Constants;

/**
 * Goods Receipt Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRRow extends GenericRow
{

    private static $instance = null;

    // Specific Attributes
    // =================================
    protected $grDate;

    protected $reversalReason;

    protected $reversalDoc;

    protected $flow;

    protected $gr;

    protected $apInvoiceRow;

    protected $poRow;

    protected $poId;

    protected $poToken;

    protected $apId;

    protected $apToken;

    /**
     *
     * @return mixed
     */
    public function getPoId()
    {
        return $this->poId;
    }

    /**
     *
     * @return mixed
     */
    public function getPoToken()
    {
        return $this->poToken;
    }

    /**
     *
     * @return mixed
     */
    public function getApId()
    {
        return $this->apId;
    }

    /**
     *
     * @return mixed
     */
    public function getApToken()
    {
        return $this->apToken;
    }

    private function __construct()
    {}

    /**
     *
     * @return NULL|object
     */
    public function makeDetailsDTO()
    {
        $dto = new GrRowDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @param GRRowSnapshot $snapshot
     * @return NULL|\Procure\Domain\GoodsReceipt\GRRow
     */
    public static function makeFromSnapshot(GRRowSnapshot $snapshot)
    {
        if (! $snapshot instanceof GRRowSnapshot) {
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
        return SnapshotAssembler::createSnapshotFrom($this, new GRRowSnapshot());
    }

    /**
     *
     * @param PORow $sourceObj
     * @throws InvalidArgumentException
     * @return \Procure\Domain\GoodsReceipt\GRRow
     */
    public static function createFromPoRow(PORow $sourceObj, CommandOptions $options)
    {
        if (! $sourceObj instanceof PORow) {
            throw new InvalidArgumentException("PO document is required!");
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRRow $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        $instance->setDocType(\Procure\Domain\Shared\Constants::PROCURE_DOC_TYPE_GR_FROM_PO); // important.

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $instance->initRow($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        return $instance;
    }

    /**
     *
     * @param GRDoc $rootEntity
     * @param APRow $sourceObj
     * @param CommandOptions $options
     * @throws InvalidArgumentException
     * @return \Procure\Domain\GoodsReceipt\GRRow
     */
    public static function copyFromApRow(GRDoc $rootEntity, APRow $sourceObj, CommandOptions $options)
    {
        if (! $rootEntity instanceof GRDoc) {
            throw new InvalidArgumentException("GR document is required!");
        }
        if (! $sourceObj instanceof APRow) {
            throw new InvalidArgumentException("AP document is required!");
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRRow $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $instance->initRow($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $instance->setApInvoiceRow($sourceObj->getId()); // important
        $instance->setInvoice($sourceObj->getDocId());
        $instance->setDocType(Constants::PROCURE_DOC_TYPE_GR_FROM_INVOICE); // important.

        return $instance;
    }

    /**
     *
     * @param GRDoc $rootEntity
     * @param APRow $sourceObj
     * @param CommandOptions $options
     * @throws InvalidArgumentException
     * @return \Procure\Domain\GoodsReceipt\GRRow
     */
    public static function copyFromApRowReserval(GRDoc $rootEntity, APRow $sourceObj, CommandOptions $options)
    {
        if (! $rootEntity instanceof GRDoc) {
            throw new InvalidArgumentException("GR document is required!");
        }
        if (! $sourceObj instanceof APRow) {
            throw new InvalidArgumentException("AP document is required!");
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRRow $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $instance->initRow($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $instance->setApInvoiceRow($sourceObj->getId()); // important
        $instance->setInvoice($sourceObj->getDocId()); // important
        $instance->setDocType(Constants::PROCURE_DOC_TYPE_GR_FROM_INVOICE); // important.

        return $instance;
    }

    /**
     *
     * @return \Procure\Domain\GoodsReceipt\GRRow
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new GRRow();
        }
        return self::$instance;
    }

    public static function createInstance()
    {
        return new GRRow();
    }

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

        $props = $reflectionClass->getProperties();

        foreach ($props as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "public $" . $propertyName . ";";
        }
    }

    /**
     *
     * @return mixed
     */
    public function getGrDate()
    {
        return $this->grDate;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalReason()
    {
        return $this->reversalReason;
    }

    /**
     *
     * @return mixed
     */
    public function getReversalDoc()
    {
        return $this->reversalDoc;
    }

    /**
     *
     * @return mixed
     */
    public function getFlow()
    {
        return $this->flow;
    }

    /**
     *
     * @return mixed
     */
    public function getGr()
    {
        return $this->gr;
    }

    /**
     *
     * @return mixed
     */
    public function getApInvoiceRow()
    {
        return $this->apInvoiceRow;
    }

    /**
     *
     * @return mixed
     */
    public function getPoRow()
    {
        return $this->poRow;
    }

    /**
     *
     * @param \Procure\Domain\GoodsReceipt\GRRow $instance
     */
    protected static function setInstance($instance)
    {
        GRRow::$instance = $instance;
    }

    /**
     *
     * @param mixed $grDate
     */
    protected function setGrDate($grDate)
    {
        $this->grDate = $grDate;
    }

    /**
     *
     * @param mixed $reversalReason
     */
    protected function setReversalReason($reversalReason)
    {
        $this->reversalReason = $reversalReason;
    }

    /**
     *
     * @param mixed $reversalDoc
     */
    protected function setReversalDoc($reversalDoc)
    {
        $this->reversalDoc = $reversalDoc;
    }

    /**
     *
     * @param mixed $flow
     */
    protected function setFlow($flow)
    {
        $this->flow = $flow;
    }

    /**
     *
     * @param mixed $gr
     */
    protected function setGr($gr)
    {
        $this->gr = $gr;
    }

    /**
     *
     * @param mixed $apInvoiceRow
     */
    protected function setApInvoiceRow($apInvoiceRow)
    {
        $this->apInvoiceRow = $apInvoiceRow;
    }

    /**
     *
     * @param mixed $poRow
     */
    protected function setPoRow($poRow)
    {
        $this->poRow = $poRow;
    }
}

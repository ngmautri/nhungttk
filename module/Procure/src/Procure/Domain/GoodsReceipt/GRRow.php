<?php
namespace Procure\Domain\GoodsReceipt;

use Procure\Domain\AbstractRow;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Domain\Exception\Gr\GrInvalidArgumentException;
use Procure\Domain\Shared\ProcureDocStatus;
use Ramsey\Uuid\Uuid;

/**
 * Goods Receipt Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRRow extends AbstractRow
{

    private static $instance = null;

    // Addtional Properties
    protected $grDate;

    protected $reversalReason;

    protected $reversalDoc;

    protected $flow;

    protected $gr;

    protected $apInvoiceRow;

    protected $poRow;

    private function __construct()
    {}

    /**
     *
     * @param PORow $sourceObj
     * @throws GrInvalidArgumentException
     * @return \Procure\Domain\GoodsReceipt\GRRow
     */
    public static function createFromPoRow(PORow $sourceObj)
    {
        if (! $sourceObj instanceof PORow) {
            throw new GrInvalidArgumentException("PO document is required!");
        }

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRRow $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);
        $instance->setPoRow($sourceObj->getId());
        $instance->setIsDraft(1);
        $instance->setIsPosted(0);
        $instance->setDocStatus(ProcureDocStatus::DOC_STATUS_DRAFT);
        $instance->setUuid(Uuid::uuid4()->toString());
        $instance->setToken($instance->getUuid());

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
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
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

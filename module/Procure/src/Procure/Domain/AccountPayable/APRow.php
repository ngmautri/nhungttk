<?php
namespace Procure\Domain\AccountPayable;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Application\DTO\Ap\ApRowDTO;
use Procure\Domain\GenericDoc;
use Procure\Domain\GenericRow;
use Procure\Domain\Contracts\ProcureDocStatus;
use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\PORow;
use Webmozart\Assert\Assert;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;

/**
 * AP Row
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class APRow extends GenericRow
{

    private static $instance = null;

    // Specific Attributes
    // =================================
    protected $reversalReason;

    protected $reversalDoc;

    protected $isReversable;

    protected $grRow;

    protected $poRow;

    protected $poId;

    protected $poToken;

    protected $grId;

    protected $grToken;

    public function markRowAsChanged(GenericAP $rootDoc, $postedBy, $postedDate)
    {
        $this->createVO($rootDoc); // createVO

        $this->setLastchangeOn($postedDate);
        $this->setLastchangeBy($postedBy);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericRow::createVO()
     */
    protected function createVO(GenericDoc $rootDoc)
    {
        $this->createUomVO();
        $this->createQuantityVO();
        $this->createDocPriceVO($rootDoc);
        $this->createLocalPriceVO($rootDoc);
    }

    /**
     *
     * @param GenericAP $rootDoc
     * @param APRowSnapshot $snapshot
     * @return \Procure\Domain\AccountPayable\APRow
     */
    public static function createFromSnapshot(GenericAP $rootDoc, APRowSnapshot $snapshot)
    {
        Assert::isInstanceOf($rootDoc, GenericAP::class, "AP doc is required!");
        Assert::isInstanceOf($snapshot, APRowSnapshot::class, "AP row snapshot is required!");

        $instance = new self();
        GenericObjectAssembler::updateAllFieldsFrom($instance, $snapshot);
        $instance->createVO($rootDoc); // important
        return $instance;
    }

    /**
     *
     * @return mixed
     */
    public function getGrId()
    {
        return $this->grId;
    }

    /**
     *
     * @return mixed
     */
    public function getGrToken()
    {
        return $this->grToken;
    }

    // =================================
    private function __construct()
    {}

    /**
     *
     * @param APRowSnapshot $snapshot
     * @return NULL|\Procure\Domain\AccountPayable\APRow
     */
    public static function makeFromSnapshot(APRowSnapshot $snapshot)
    {
        if (! $snapshot instanceof APRowSnapshot) {
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
        return APRowSnapshotAssembler::updateAllFieldsFrom(new APRowSnapshot(), $this);
    }

    /**
     *
     * @return NULL|object
     */
    public function makeDetailsDTO()
    {
        $dto = new ApRowDTO();
        $dto = DTOFactory::createDTOFrom($this, $dto);
        return $dto;
    }

    /**
     *
     * @param APFromPO $rootDoc
     * @param PORow $sourceObj
     * @param CommandOptions $options
     * @throws InvalidArgumentException
     * @return \Procure\Domain\AccountPayable\APRow
     */
    public static function createFromPoRow(APFromPO $rootDoc, PORow $sourceObj, CommandOptions $options)
    {
        Assert::isInstanceOf($rootDoc, APFromPO::class, "AP-PO doc is required!");
        Assert::isInstanceOf($sourceObj, PORow::class, "PO document  snapshot is required!");
        Assert::notNull($options, "No command options is found");

        /**
         *
         * @var APRow $instance
         */
        $instance = new self();

        $instance = $sourceObj->convertTo($instance);
        $instance->initRow($options);

        $instance->setDocType(ProcureDocType::INVOICE_FROM_PO); // important.
        $instance->setPoRow($sourceObj->getId()); // Important
        $instance->setGlAccount($sourceObj->getItemInventoryGL());
        $instance->setCostCenter($sourceObj->getItemCostCenter());
        $instance->setWarehouse($sourceObj->getWarehouse());

        return $instance;
    }

    /**
     *
     * @param APDoc $rootEntity
     * @param APRow $sourceObj
     * @param CommandOptions $options
     * @throws InvalidArgumentException
     * @return \Procure\Domain\AccountPayable\APRow
     */
    public static function createRowReversal(GenericAP $rootEntity, APRow $sourceObj, CommandOptions $options)
    {
        Assert::isInstanceOf($rootEntity, GenericAP::class, "AP doc is required!");
        Assert::isInstanceOf($sourceObj, APRow::class, "AP row is required!");
        Assert::notNull($options, "No command options is found");

        /**
         *
         * @var APRow $instance
         */

        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        $instance->initRow($options);

        $instance->setDocType($rootEntity->getDocType()); // important.
        $instance->setReversalDoc($sourceObj->getId()); // Important
        $instance->setInvoice($rootEntity->getId());

        $instance->markAsReversed($options);

        return $instance;
    }

    /**
     *
     * @return \Procure\Domain\AccountPayable\APRow
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new APRow();
        }
        return self::$instance;
    }

    /**
     *
     * @return \Procure\Domain\AccountPayable\APRow
     */
    public static function createInstance()
    {
        return new APRow();
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
     * @param mixed $isReversable
     */
    protected function setIsReversable($isReversable)
    {
        $this->isReversable = $isReversable;
    }

    /**
     *
     * @param mixed $grRow
     */
    protected function setGrRow($grRow)
    {
        $this->grRow = $grRow;
    }

    /**
     *
     * @param mixed $poRow
     */
    protected function setPoRow($poRow)
    {
        $this->poRow = $poRow;
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
    public function getIsReversable()
    {
        return $this->isReversable;
    }

    /**
     *
     * @return mixed
     */
    public function getGrRow()
    {
        return $this->grRow;
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
}

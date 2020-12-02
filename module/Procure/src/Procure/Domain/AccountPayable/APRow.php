<?php
namespace Procure\Domain\AccountPayable;

use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Application\DTO\Ap\ApRowDTO;
use Procure\Domain\GenericRow;
use Procure\Domain\Contracts\ProcureDocStatus;
use Procure\Domain\Contracts\ProcureDocType;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\PORow;
use Webmozart\Assert\Assert;

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

    public function markRowAsPosted(GenericAP $rootDoc, $postedBy, $postedDate)
    {
        $this->setLastchangeOn($postedDate);
        $this->setLastchangeBy($postedBy);
        $this->setIsPosted(1);
        $this->setIsActive(1);
        $this->setIsDraft(0);
        $this->setIsReversed(0);
        $this->setDocStatus(ProcureDocStatus::POSTED);

        $this->createVO($rootDoc); // createVO
    }

    public function markRowAsChanged(GenericAP $rootDoc, $postedBy, $postedDate)
    {
        $this->setLastchangeOn($postedDate);
        $this->setLastchangeBy($postedBy);

        $this->createVO($rootDoc); // createVO
    }

    public function markRowAsReversed(GenericAP $rootDoc, $postedBy, $postedDate)
    {
        $this->setLastchangeBy($postedBy);
        $this->setLastchangeOn($postedDate);
        $this->setIsReversed(1);
        $this->setIsActive(1);
        $this->setIsDraft(0);
        $this->setIsPosted(0);
        $this->setDocStatus(ProcureDocStatus::REVERSED);

        $this->createVO($rootDoc); // createVO
    }

    protected function createVO(GenericAP $rootDoc)
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
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
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
        $instance->setDocType(ProcureDocType::INVOICE_FROM_PO); // important.
        $instance->setPoRow($sourceObj->getId()); // Important

        $instance->glAccount = $sourceObj->getItemInventoryGL();
        $instance->costCenter = $sourceObj->getItemCostCenter();
        $instance->setWarehouse($rootDoc->getWarehouse());

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $instance->initRow($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

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

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $instance->initRow($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
        $instance->markAsReversed($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $instance->setDocType($rootEntity->getDocType()); // important.
        $instance->setReversalDoc($sourceObj->getId()); // Important
        $instance->setInvoice($rootEntity->getId());

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

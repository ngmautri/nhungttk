<?php
namespace Procure\Domain\AccountPayable;

use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\Event\Ap\ApHeaderCreated;
use Procure\Domain\Event\Ap\ApHeaderUpdated;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\InvalidOperationException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Exception\ValidationFailedException;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\Service\APPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Shared\Constants;
use Procure\Domain\Shared\ProcureDocStatus;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Ramsey;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class APDoc extends GenericAP
{

    private static $instance = null;

    // Specific Attribute
    // ===================
    protected $reversalDoc;

    protected $isReversable;

    protected $procureGr;

    protected $po;

    protected $inventoryGr;

    // ===================
    private function __construct()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::makeSnapshot()
     */
    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new APSnapshot());
    }

    public function makeDetailsSnapshot()
    {
        $snapshot = new APSnapshot();
        $snapshot = SnapshotAssembler::createSnapshotFrom($this, $snapshot);
        return $snapshot;
    }

    /**
     *
     * @param APSnapshot $snapshot
     * @return void|\Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function makeFromSnapshot(APSnapshot $snapshot)
    {
        if (! $snapshot instanceof APSnapshot)
            return;

        if ($snapshot->uuid == null) {
            $snapshot->uuid = Ramsey\Uuid\Uuid::uuid4()->toString();
            $snapshot->token = $snapshot->uuid;
        }

        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * @param PODoc $sourceObj
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @throws InvalidArgumentException
     * @return \Procure\Domain\AccountPayable\APDoc
     */
    public static function createFromPo(PODoc $sourceObj, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators)
    {
        if (! $sourceObj instanceof PODoc) {
            throw new InvalidArgumentException("PO Entity is required");
        }

        $rows = $sourceObj->getDocRows();

        if ($rows == null) {
            throw new InvalidArgumentException("PO Entity is empty!");
        }

        if ($sourceObj->getDocStatus() !== ProcureDocStatus::DOC_STATUS_POSTED) {
            throw new InvalidArgumentException("PO document is not posted!");
        }

        if ($sourceObj->getTransactionStatus() == Constants::TRANSACTION_STATUS_COMPLETED) {
            throw new InvalidArgumentException("AP Doc is completed!");
        }

        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }
        /**
         *
         * @var APDoc $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        // overwrite.
        $instance->setDocType(\Procure\Domain\Shared\Constants::PROCURE_DOC_TYPE_INVOICE_PO); // important.

        $createdBy = $options->getUserId();
        $createdDate = new \DateTime();
        $instance->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
        $instance->validateHeader($headerValidators);

        foreach ($rows as $r) {

            /**
             *
             * @var APRow $r ;
             */

            // ignore completed row;

            $localEntity = APRow::createFromPoRow($r, $options);
            // echo sprintf("\n %s, PoRowId %s, %s" , $grRow->getItemName(), $grRow->getPoRow(), $grRow->getPrRow());
            $instance->addRow($localEntity);

            $instance->validateRow($localEntity, $rowValidators);
        }
        return $instance;
    }

    /**
     *
     * @param APSnapshot $snapshot
     * @param \Application\Domain\Shared\Command\CommandOptions $options
     * @param \Procure\Domain\Validator\HeaderValidatorCollection $headerValidators
     * @param \Procure\Domain\Validator\RowValidatorCollection $rowValidators
     * @param \Procure\Domain\Service\SharedService $sharedService
     * @param \Procure\Domain\Service\APPostingService $postingService
     * @throws \Procure\Domain\Exception\InvalidOperationException
     * @throws \Procure\Domain\Exception\InvalidArgumentException
     * @throws \Procure\Domain\Exception\OperationFailedException
     */
    public function saveFromPO(APSnapshot $snapshot, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, APPostingService $postingService)
    {
        if (! $this->getDocStatus() == ProcureDocStatus::DOC_STATUS_DRAFT) {
            throw new InvalidOperationException(sprintf("PO is already posted/closed or being amended! %s", __FUNCTION__));
        }

        if ($this->getDocRows() == null) {
            throw new InvalidOperationException(sprintf("Documment is empty! %s", __FUNCTION__));
        }

        if (! $this->getDocType() == Constants::PROCURE_DOC_TYPE_INVOICE_PO) {
            throw new InvalidOperationException(sprintf("Doctype is not vadid! %s", __FUNCTION__));
        }

        $this->_checkInputParams($snapshot, $headerValidators, $sharedService, $postingService);

        if ($rowValidators == null) {
            throw new InvalidArgumentException("HeaderValidatorCollection not found");
        }

        if ($options == null) {
            throw new InvalidArgumentException("Comnand Options not found!");
        }

        // Entity from Snapshot
        if ($snapshot !== null) {
            $this->setDocCurrency($snapshot->getDocCurrency());
            $this->setDocDate($snapshot->getDocDate());
            $this->setDocNumber($snapshot->getDocNumber());
            $this->setPostingDate($snapshot->getPostingDate());
            $this->setGrDate($snapshot->getGrDate());
            $this->setWarehouse($snapshot->getWarehouse());
            $this->setPmtTerm($snapshot->getPmtTerm());
            $this->setRemarks($snapshot->getRemarks());
        }

        $createdDate = new \Datetime();
        $this->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));

        $this->validate($headerValidators, $rowValidators);
        if ($this->hasErrors()) {
            throw new ValidationFailedException($this->getErrorMessage());
        }

        $this->clearEvents();
        $rootSnapshot = $postingService->getCmdRepository()->store($this);
        return $rootSnapshot;
    }

    /**
     *
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new APDoc();
        }
        return self::$instance;
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
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "public $" . $propertyName . ";";
        }
    }

    /**
     *
     * @param APSnapshot $snapshot
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param SharedService $sharedService
     * @param APPostingService $postingService
     * @throws InvalidArgumentException
     * @throws OperationFailedException
     * @return \Procure\Domain\AccountPayable\APDoc
     */
    public static function createFrom(APSnapshot $snapshot, CommandOptions $options, HeaderValidatorCollection $headerValidators, SharedService $sharedService, APPostingService $postingService)
    {
        $instance = new self();
        $instance->_checkInputParams($snapshot, $headerValidators, $sharedService, $postingService);

        if ($options == null) {
            throw new InvalidArgumentException("Options is null");
        }

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $instance->setExchangeRate($fxRate);

        $instance->validateHeader($headerValidators);

        if ($instance->hasErrors()) {
            throw new ValidationFailedException($instance->getNotification()->errorMessage());
        }

        $instance->setDocType(Constants::PROCURE_DOC_TYPE_INVOICE);

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $instance->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $instance->recordedEvents = array();

        /**
         *
         * @var APSnapshot $rootSnapshot
         */
        $rootSnapshot = $postingService->getCmdRepository()->storeHeader($instance, false);

        if ($rootSnapshot == null) {
            throw new OperationFailedException(sprintf("Error orcured when creating AP #%s", $instance->getId()));
        }

        $instance->id = $rootSnapshot->getId();

        $trigger = $options->getTriggeredBy();
        $params = [];

        $instance->addEvent(new ApHeaderCreated($rootSnapshot, $trigger, $params));
        return $instance;
    }

    /**
     *
     * @param APSnapshot $snapshot
     * @param \Application\Domain\Shared\Command\CommandOptions $options
     * @param array $params
     * @param \Procure\Domain\Validator\HeaderValidatorCollection $headerValidators
     * @param \Procure\Domain\Service\SharedService $sharedService
     * @param \Procure\Domain\Service\APPostingService $postingService
     * @throws \Procure\Domain\Exception\InvalidArgumentException
     * @throws \Procure\Domain\Exception\OperationFailedException
     * @return \Procure\Domain\AccountPayable\APDoc
     */
    public static function updateFrom(APSnapshot $snapshot, CommandOptions $options, $params, HeaderValidatorCollection $headerValidators, SharedService $sharedService, APPostingService $postingService)
    {
        $instance = new self();
        $instance->_checkInputParams($snapshot, $headerValidators, $sharedService, $postingService);

        if ($options == null) {
            throw new InvalidArgumentException("Opptions is null");
        }

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $instance->setExchangeRate($fxRate);

        $instance->validateHeader($headerValidators);

        if ($instance->hasErrors()) {
            throw new ValidationFailedException(sprintf("%s-%s", $instance->getNotification()->errorMessage(), __FUNCTION__));
        }

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();

        $instance->setLastchangeOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $instance->setLastchangeBy($createdBy);

        $instance->recordedEvents = array();

        /**
         *
         * @var APSnapshot $rootSnapshot
         */
        $rootSnapshot = $postingService->getCmdRepository()->storeHeader($instance, false);

        if ($rootSnapshot == null) {
            throw new OperationFailedException(sprintf("%s-%s", "Error orcured when creating AP!", __FUNCTION__));
        }

        $instance->id = $rootSnapshot->getId();

        $trigger = null;
        if ($options !== null) {
            $trigger = $options->getTriggeredBy();
        }

        $instance->addEvent(new ApHeaderUpdated($rootSnapshot, $trigger, $params));
        return $instance;
    }

    /**
     * Call this method to get from storage
     *
     * @param APSnapshot $snapshot
     * @return void|\Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function constructFromDetailsSnapshot(APSnapshot $snapshot)
    {
        if (! $snapshot instanceof APSnapshot) {
            return;
        }

        if ($snapshot->uuid == null) {
            $snapshot->uuid = Ramsey\Uuid\Uuid::uuid4()->toString();
        }
        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /**
     * Call this method to get from storage
     *
     * @param APSnapshot $snapshot
     * @return void|\Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function constructFromSnapshot(APSnapshot $snapshot)
    {
        if (! $snapshot instanceof APSnapshot) {
            return;
        }

        if ($snapshot->uuid == null) {
            $snapshot->uuid = Ramsey\Uuid\Uuid::uuid4()->toString();
            $snapshot->token = $snapshot->uuid;
        }

        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\GenericGR::doPost()
     */
    protected function doPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, APPostingService $postingService)
    {
        /**
         *
         * @var APRow $row ;
         */
        $postedDate = new \Datetime();

        $this->markAsPosted($options->getUserId(), date_format($postedDate, 'Y-m-d H:i:s'));

        foreach ($this->getDocRows() as $row) {

            if ($row->getDocQuantity() == 0) {
                continue;
            }

            $row->markAsPosted($options->getUserId(), date_format($postedDate, 'Y-m-d H:i:s'));
        }

        $this->validate($headerValidators, $rowValidators, true);

        if ($this->hasErrors()) {
            throw new ValidationFailedException(sprintf("%s-%s", $this->getNotification()->errorMessage(), __FUNCTION__));
        }

        $postingService->getCmdRepository()->post($this, true);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\GenericGR::doReverse()
     */
    protected function doReverse(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, APPostingService $postingService)
    {
        /**
         *
         * @var APRow $row ;
         */
        $postedDate = new \Datetime();
        $this->markAsReversed($options->getUserId(), date_format($postedDate, 'Y-m-d H:i:s'));

        foreach ($this->getDocRows() as $row) {

            if ($row->getDocQuantity() == 0) {
                continue;
            }

            $row->markAsReversed($options->getUserId(), date_format($postedDate, 'Y-m-d H:i:s'));
        }

        $this->validate($headerValidators, $rowValidators, true);

        if ($this->hasErrors()) {
            throw new ValidationFailedException(sprintf("%s-%s", $this->getNotification()->errorMessage(), __FUNCTION__));
        }

        $postingService->getCmdRepository()->post($this, false);
    }

    /**
     *
     * @param APSnapshot $snapshot
     * @param \Procure\Domain\Validator\HeaderValidatorCollection $headerValidators
     * @param \Procure\Domain\Service\SharedService $sharedService
     * @param \Procure\Domain\Service\APPostingService $postingService
     * @throws \Procure\Domain\Exception\Gr\GrInvalidArgumentException
     */
    private function _checkInputParams(APSnapshot $snapshot, HeaderValidatorCollection $headerValidators, SharedService $sharedService, APPostingService $postingService)
    {
        if (! $snapshot instanceof APSnapshot) {
            throw new InvalidArgumentException("APSnapshot not found!");
        }

        if ($headerValidators == null) {
            throw new InvalidArgumentException("HeaderValidatorCollection not found");
        }
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }

        if ($postingService == null) {
            throw new InvalidArgumentException("postingService service not found");
        }
    }

    protected function afterPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, APPostingService $postingService)
    {}

    protected function prePost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, APPostingService $postingService)
    {}

    protected function preReserve(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, APPostingService $postingService)
    {}

    protected function afterReserve(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, APPostingService $postingService)
    {}

    protected function raiseEvent()
    {}

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
     * @param mixed $procureGr
     */
    protected function setProcureGr($procureGr)
    {
        $this->procureGr = $procureGr;
    }

    /**
     *
     * @param mixed $po
     */
    protected function setPo($po)
    {
        $this->po = $po;
    }

    /**
     *
     * @param mixed $inventoryGr
     */
    protected function setInventoryGr($inventoryGr)
    {
        $this->inventoryGr = $inventoryGr;
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
    public function getProcureGr()
    {
        return $this->procureGr;
    }

    /**
     *
     * @return mixed
     */
    public function getPo()
    {
        return $this->po;
    }

    /**
     *
     * @return mixed
     */
    public function getInventoryGr()
    {
        return $this->inventoryGr;
    }
}
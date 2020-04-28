<?php
namespace Procure\Domain\QuotationRequest;

use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Exception\ValidationFailedException;
use Procure\Domain\Service\QrPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Shared\Constants;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Ramsey\Uuid\Uuid;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QRDoc extends GenericQR
{

    private static $instance = null;

    // Specific Attribute
    // ===================

    // ====================
    private function __construct()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::makeSnapshot()
     */
    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new QRSnapshot());
    }

    public function makeDetailsSnapshot()
    {
        $snapshot = new QRSnapshot();
        $snapshot = SnapshotAssembler::createSnapshotFrom($this, $snapshot);
        return $snapshot;
    }

    /**
     *
     * @param QRSnapshot $snapshot
     * @return void|\Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function makeFromSnapshot(QRSnapshot $snapshot)
    {
        if (! $snapshot instanceof QRSnapshot)
            return;

        if ($snapshot->uuid == null) {
            $snapshot->uuid = Uuid::uuid4()->toString();
        }

        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new QRDoc();
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

    public static function createFrom(QRSnapshot $snapshot, CommandOptions $options, HeaderValidatorCollection $headerValidators, SharedService $sharedService, QrPostingService $postingService)
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
         * @var QRSnapshot $rootSnapshot
         */
        $rootSnapshot = $postingService->getCmdRepository()->storeHeader($instance, false);

        if ($rootSnapshot == null) {
            throw new OperationFailedException(sprintf("Error orcured when creating AP #%s", $instance->getId()));
        }

        $instance->id = $rootSnapshot->getId();

        $trigger = $options->getTriggeredBy();
        $params = [];

        $instance->addEvent();
        return $instance;
    }

    public static function updateFrom(QRSnapshot $snapshot, CommandOptions $options, $params, HeaderValidatorCollection $headerValidators, SharedService $sharedService, QrPostingService $postingService)
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
         * @var QRSnapshot $rootSnapshot
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

        $instance->addEvent();
        return $instance;
    }

    /**
     * Call this method to get from storage
     *
     * @param QRSnapshot $snapshot
     * @return void|\Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function constructFromDetailsSnapshot(QRSnapshot $snapshot)
    {
        if (! $snapshot instanceof QRSnapshot) {
            return;
        }

        if ($snapshot->uuid == null) {
            $snapshot->uuid = Uuid::uuid4()->toString();
        }
        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /**
     * Call this method to get from storage
     *
     * @param QRSnapshot $snapshot
     * @return void|\Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function constructFromSnapshot(QRSnapshot $snapshot)
    {
        if (! $snapshot instanceof QRSnapshot) {
            return;
        }

        if ($snapshot->uuid == null) {
            $snapshot->uuid = Uuid::uuid4()->toString();
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
    protected function doPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService)
    {
        /**
         *
         * @var QRRow $row ;
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
    protected function doReverse(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService)
    {
        /**
         *
         * @var QRRow $row ;
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

    private function _checkInputParams(QRSnapshot $snapshot, HeaderValidatorCollection $headerValidators, SharedService $sharedService, QrPostingService $postingService)
    {
        if (! $snapshot instanceof QRSnapshot) {
            throw new InvalidArgumentException("QRSnapshot not found!");
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

    protected function afterPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService)
    {}

    protected function prePost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService)
    {}

    protected function preReserve(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService)
    {}

    protected function afterReserve(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, QrPostingService $postingService)
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
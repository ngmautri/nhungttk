<?php
namespace Procure\Domain\GoodsReceipt;

use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Application\Command\PO\Options\PoCreateOptions;
use Procure\Application\Command\PO\Options\PoUpdateOptions;
use Procure\Domain\Event\Gr\GrHeaderCreated;
use Procure\Domain\Event\Gr\GrHeaderUpdated;
use Procure\Domain\Exception\PoCreateException;
use Procure\Domain\Exception\PoInvalidArgumentException;
use Procure\Domain\Exception\PoUpdateException;
use Procure\Domain\Exception\Gr\GrCreateException;
use Procure\Domain\Exception\Gr\GrInvalidArgumentException;
use Procure\Domain\Exception\Gr\GrInvalidOperationException;
use Procure\Domain\Exception\Gr\GrPostingException;
use Procure\Domain\Exception\Gr\GrUpdateException;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\Service\GrPostingService;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Shared\Constants;
use Procure\Domain\Shared\ProcureDocStatus;
use Procure\Domain\Validator\HeaderValidatorCollection;
use Procure\Domain\Validator\RowValidatorCollection;
use Ramsey\Uuid\Uuid;
use Ramsey;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRDoc extends GenericGR
{

    private static $instance = null;

    // Specific Attribute
    protected $reversalDoc;

    private function __construct()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::makeSnapshot()
     */
    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new GRSnapshot());
    }

    /**
     *
     * @param PODoc $sourceObj
     * @throws GrInvalidArgumentException
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function createFromPo(PODoc $sourceObj, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators)
    {
        if (! $sourceObj instanceof PODoc) {
            throw new GrInvalidArgumentException("PO Entity is required");
        }

        $rows = $sourceObj->getDocRows();

        if ($rows == null) {
            throw new GrInvalidOperationException("PO Entity  is empty!");
        }

        if ($sourceObj->getDocStatus() !== ProcureDocStatus::DOC_STATUS_POSTED) {
            throw new GrInvalidOperationException("PO document is not posted!");
        }

        if ($sourceObj->getTransactionStatus() == \Procure\Domain\Shared\Constants::TRANSACTION_STATUS_COMPLETED) {
            throw new GrInvalidOperationException("GR is completed!");
        }

        if ($options == null) {
            throw new GrInvalidOperationException("No Options is found");
        }
        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRDoc $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        // overwrite.
        $instance->setDocType(\Procure\Domain\Shared\Constants::PROCURE_DOC_TYPE_GR_FROM_PO); // important.
        $instance->setIsDraft(1);
        $instance->setIsPosted(0);
        $instance->setDocVersion(0);
        $instance->setRevisionNo(0);
        $instance->setDocStatus(ProcureDocStatus::DOC_STATUS_DRAFT);
        $instance->setUuid(Uuid::uuid4()->toString());
        $instance->setToken($instance->getUuid());
        $instance->setCreatedBy($options->getUserId());

        $instance->validateHeader($headerValidators);

        foreach ($rows as $r) {

            /**
             *
             * @var \Procure\Domain\PurchaseOrder\PORow $r ;
             */

            // ignore completed row;
            if ($r->getOpenGrBalance() == 0) {
                continue;
            }

            $grRow = GrRow::createFromPoRow($r);
            // echo sprintf("\n %s, PoRowId %s, %s" , $grRow->getItemName(), $grRow->getPoRow(), $grRow->getPrRow());
            $instance->addRow($grRow);

            $instance->validateRow($grRow, $rowValidators);
        }
        return $instance;
    }

    /**
     *
     * @param CommandOptions $options
     * @param GRSnapshot $snapshot
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param GrPostingService $postingService
     * @throws GrInvalidOperationException
     * @throws GrInvalidArgumentException
     * @throws GrPostingException
     */
    public function saveFromPO(GRSnapshot $snapshot, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService)
    {
        if (! $this->getDocStatus() == GRDocStatus::DOC_STATUS_DRAFT) {
            throw new GrInvalidOperationException(sprintf("PO is already posted/closed or being amended! %s", $this->getId()));
        }

        if (! $this->getDocType() == Constants::PROCURE_DOC_TYPE_GR_FROM_PO) {
            throw new GrInvalidOperationException(sprintf("Doctype is not vadid! %s", $this->getDocType()));
        }

        if ($headerValidators == null) {
            throw new GrInvalidArgumentException("HeaderValidatorCollection not found");
        }

        if ($rowValidators == null) {
            throw new GrInvalidArgumentException("HeaderValidatorCollection not found");
        }

        if ($sharedService == null) {
            throw new GrInvalidArgumentException("SharedService service not found");
        }

        if ($postingService == null) {
            throw new GrInvalidArgumentException("postingService service not found");
        }

        if ($options == null) {
            throw new GrInvalidArgumentException("Comnand Options not found!");
        }

        // Update Good Receipt Date and WH
        if ($snapshot !== null) {
            $this->setGrDate($snapshot->getGrDate());
            $this->setWarehouse($snapshot->getWarehouse());
        }
        
        $createdDate = new \Datetime();
        $this->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));
        
        $this->validate($headerValidators, $rowValidators);
        if ($this->hasErrors()) {
            throw new GrPostingException($this->getErrorMessage());
        }

        $this->clearEvents();
        echo "Test";
        
        
        $rootSnapshot = $postingService->getCmdRepository()->store($this);
        
        echo "Test end";
        var_dump($rootSnapshot);
      }

    /**
     *
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new GRDoc();
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
     * @param GRSnapshot $snapshot
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param SharedService $sharedService
     * @param GrPostingService $postingService
     * @throws GrInvalidArgumentException
     * @throws PoCreateException
     * @throws PoUpdateException
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function createFrom(GRSnapshot $snapshot, CommandOptions $options, HeaderValidatorCollection $headerValidators, SharedService $sharedService, GrPostingService $postingService)
    {
        if (! $snapshot instanceof GRSnapshot) {
            throw new GrInvalidArgumentException("PO snapshot not found!");
        }

        if (! $headerValidators instanceof HeaderValidatorCollection) {
            throw new GrInvalidArgumentException("Header Validator not found!");
        }

        if (! $sharedService instanceof SharedService) {
            throw new GrInvalidArgumentException("Shared service not found!");
        }

        if (! $postingService instanceof GrPostingException) {
            throw new GrInvalidArgumentException("Posting service not found!");
        }

        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $instance->setExchangeRate($fxRate);

        $instance->validateHeader($headerValidators);

        if ($instance->hasErrors()) {
            throw new GrCreateException($instance->getNotification()->errorMessage());
        }

        $createdDate = new \Datetime();
        $instance->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $instance->setDocStatus(GRDocStatus::DOC_STATUS_DRAFT);
        $instance->setDocType(GRDocType::PO);
        $instance->setIsActive(1);
        $instance->setSysNumber(Constants::SYS_NUMBER_UNASSIGNED);
        $instance->setRevisionNo(1);
        $instance->setDocVersion(1);
        $instance->setUuid(Ramsey\Uuid\Uuid::uuid4()->toString());
        $instance->setToken($instance->getUuid());

        $instance->recordedEvents = array();

        /**
         *
         * @var GRSnapshot $rootSnapshot
         */
        $rootSnapshot = $postingService->getCmdRepository()->storeHeader($instance, false);

        if ($rootSnapshot == null) {
            throw new GrUpdateException(sprintf("Error orcured when creating PO #%s", $instance->getId()));
        }

        $instance->id = $rootSnapshot->getId();

        $trigger = null;
        $params = null;
        if ($options instanceof PoCreateOptions) {
            $trigger = $options->getTriggeredBy();
            $params = [];
        }

        $instance->addEvent(new GrHeaderCreated($rootSnapshot, $trigger, $params));
        return $instance;
    }

    /**
     *
     * @param GrSnapshot $snapshot
     * @param CommandOptions $options
     * @param array $params
     * @param HeaderValidatorCollection $headerValidators
     * @param SharedService $sharedService
     * @param POPostingService $postingService
     * @throws PoInvalidArgumentException
     * @throws GrCreateException
     * @throws GrUpdateException
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function updateFrom(GrSnapshot $snapshot, CommandOptions $options, $params, HeaderValidatorCollection $headerValidators, SharedService $sharedService, POPostingService $postingService)
    {
        if (! $snapshot instanceof GrSnapshot) {
            throw new PoInvalidArgumentException("PO snapshot not found!");
        }

        if (! $headerValidators instanceof HeaderValidatorCollection) {
            throw new PoInvalidArgumentException("Header Validator not found!");
        }

        if (! $sharedService instanceof SharedService) {
            throw new PoInvalidArgumentException("Shared service not found!");
        }

        if (! $postingService instanceof POPostingService) {
            throw new PoInvalidArgumentException("Posting service not found!");
        }

        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $instance->setExchangeRate($fxRate);

        $instance->validateHeader($headerValidators);

        if ($instance->hasErrors()) {
            throw new GrCreateException($instance->getNotification()->errorMessage());
        }

        $createdDate = new \Datetime();
        $instance->setLastchangeOn(date_format($createdDate, 'Y-m-d H:i:s'));

        $instance->recordedEvents = array();

        /**
         *
         * @var GRSnapshot $rootSnapshot
         */
        $rootSnapshot = $postingService->getCmdRepository()->storeHeader($instance, false);

        if ($rootSnapshot == null) {
            throw new GrUpdateException(sprintf("Error orcured when creating PO #%s", $instance->getId()));
        }

        $instance->id = $rootSnapshot->getId();

        $trigger = null;
        if ($options instanceof PoUpdateOptions) {
            $trigger = $options->getTriggeredBy();
        }

        $instance->addEvent(new GrHeaderUpdated($rootSnapshot, $trigger, $params));
        return $instance;
    }

    /**
     * Call this method to get from storage
     *
     * @param GRDetailsSnapshot $snapshot
     * @return void|\Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function constructFromDetailsSnapshot(GRDetailsSnapshot $snapshot)
    {
        if (! $snapshot instanceof GRDetailsSnapshot)
            return;

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
     * @param GrSnapshot $snapshot
     * @return void|\Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function constructFromSnapshot(GrSnapshot $snapshot)
    {
        if (! $snapshot instanceof GrSnapshot)
            return;

        if ($snapshot->uuid == null) {
            $snapshot->uuid = Ramsey\Uuid\Uuid::uuid4()->toString();
            $snapshot->token = $snapshot->uuid;
        }

        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    protected function afterPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {}

    protected function doReverse(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {}

    protected function prePost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {}

    protected function preReserve(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {}

    protected function afterReserve(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {}

    protected function raiseEvent()
    {}

    protected function doPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {}
}
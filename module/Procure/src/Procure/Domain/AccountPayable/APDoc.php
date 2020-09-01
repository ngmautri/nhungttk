<?php
namespace Procure\Domain\AccountPayable;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\Event\Ap\ApHeaderCreated;
use Procure\Domain\Event\Ap\ApHeaderUpdated;
use Procure\Domain\Event\Ap\ApReservalCreated;
use Procure\Domain\Event\Ap\ApReversed;
use Procure\Domain\Exception\InvalidOperationException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Service\Contracts\ValidationServiceInterface;
use Procure\Domain\Shared\Constants;
use Procure\Domain\Shared\ProcureDocStatus;
use Ramsey\Uuid\Uuid;
use InvalidArgumentException;
use RuntimeException;

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
        if (! $snapshot instanceof APSnapshot) {
            return;
        }

        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * @param PODoc $sourceObj
     * @param CommandOptions $options
     * @param ValidationServiceInterface $validationService
     * @throws InvalidArgumentException
     * @return \Procure\Domain\AccountPayable\APDoc
     */
    public static function createFromPo(PODoc $sourceObj, CommandOptions $options, ValidationServiceInterface $validationService)
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
        $instance->validateHeader($validationService->getHeaderValidators());

        foreach ($rows as $r) {

            /**
             *
             * @var APRow $r ;
             */
            $localEntity = APRow::createFromPoRow($instance, $r, $options);
            $instance->addRow($localEntity);

            $instance->validateRow($localEntity, $validationService->getRowValidators());
        }
        return $instance;
    }

    /**
     *
     * @param APDoc $sourceObj
     * @param APSnapshot $snapshot
     * @param CommandOptions $options
     * @param ValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     * @throws InvalidOperationException
     * @throws OperationFailedException
     * @throws \RuntimeException
     * @return \Procure\Domain\AccountPayable\APDoc
     */
    public static function createAndPostReserval(APDoc $sourceObj, APSnapshot $snapshot, CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {
        if (! $sourceObj instanceof APDoc) {
            throw new InvalidArgumentException("AP Doc is required");
        }

        if ($sourceObj->getDocStatus() !== ProcureDocStatus::DOC_STATUS_POSTED) {
            throw new InvalidOperationException("AP document is not posted yet!");
        }

        $rows = $sourceObj->getDocRows();

        if (empty($rows)) {
            throw new InvalidArgumentException("AP Doc is empty!");
        }

        /**
         *
         * @var APDoc $instance
         * @var APRow $r ;
         */
        $instance = new self();

        // check params
        $instance->_checkParams($snapshot, $validationService, $sharedService);

        // also row validation needed.
        if ($validationService->getRowValidators() == null) {
            throw new InvalidArgumentException("Rows validators not found");
        }

        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        $reversalDate = $snapshot->getReversalDate();
        $createdDate = new \DateTime();
        $createdBy = $options->getUserId();

        $instance = $sourceObj->convertTo($instance);
        $instance->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
        $instance->markAsReversed($createdBy, $reversalDate);

        // overwrite.
        $instance->setReversalDoc($sourceObj->getId()); // Important
        $instance->setDocType(sprintf("%s-1", "REV")); // important.

        $instance->validateHeader($validationService->getHeaderValidators());

        // $sourceObj
        $sourceObj->markAsReversed($createdBy, $reversalDate);
        $sourceObj->validateHeader($validationService->getHeaderValidators());

        foreach ($rows as $r) {

            // $sourceObj
            $r->markAsReversed($createdBy, $reversalDate);
            $sourceObj->validateRow($r, $validationService->getRowValidators());

            $localEntity = APRow::createRowReserval($instance, $r, $options);

            $instance->addRow($localEntity);
            $instance->validateRow($localEntity, $validationService->getRowValidators());
        }

        if ($instance->hasErrors()) {
            throw new \RuntimeException($instance->getErrorMessage());
        }

        if ($sourceObj->hasErrors()) {
            throw new RuntimeException($sourceObj->getErrorMessage());
        }

        $instance->clearEvents();
        $sourceObj->clearEvents();

        // Post new object
        $snapshot = $sharedService->getPostingService()
            ->getCmdRepository()
            ->post($instance, true);

        if (! $snapshot instanceof APSnapshot) {
            throw new \RuntimeException(sprintf("Error orcured when reveral AP #%s", $sourceObj->getId()));
        }

        $instance->setId($snapshot->getId());
        $instance->setToken($snapshot->getToken());

        // Post source object
        $sharedService->getPostingService()
            ->getCmdRepository()
            ->post($sourceObj, false);

        $target = $snapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($snapshot->getId());
        $defaultParams->setTargetToken($snapshot->getToken());
        $defaultParams->setTargetDocVersion($snapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($snapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;
        $e1 = new ApReservalCreated($target, $defaultParams, $params);

        $target = $sourceObj;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($sourceObj->getId());
        $defaultParams->setTargetToken($sourceObj->getToken());
        $defaultParams->setTargetDocVersion($sourceObj->getDocVersion());
        $defaultParams->setTargetRrevisionNo($sourceObj->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;
        $e2 = new ApReversed($target, $defaultParams, $params);

        $instance->addEvent($e1);
        $instance->addEvent($e2);

        $sourceObj->addEvent($e1);
        $sourceObj->addEvent($e2);

        return $instance;
    }

    /**
     *
     * @param APSnapshot $snapshot
     * @param CommandOptions $options
     * @param ValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws InvalidOperationException
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     * @return \Procure\Domain\AccountPayable\APSnapshot
     */
    public function saveFromPO(APSnapshot $snapshot, CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
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

        if ($options == null) {
            throw new InvalidArgumentException("Comnand Options not found!");
        }

        $this->_checkParams($snapshot, $validationService, $sharedService);

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
            $this->setCompany($options->getCompanyId());
        }

        // update warehouse for row.
        $this->refreshRows($snapshot);

        $createdDate = new \Datetime();
        $this->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));

        $this->validate($validationService->getHeaderValidators(), $validationService->getRowValidators());
        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getErrorMessage());
        }

        $this->clearEvents();
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->store($this);

        if (! $rootSnapshot instanceof APSnapshot) {
            throw new \RuntimeException(\sprintf("Errors occured when saving AP"));
        }

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetDocVersion($rootSnapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new ApHeaderCreated($target, $defaultParams, $params);
        $this->addEvent($event);

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
     * @param ValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     * @throws \RangeException
     * @throws \RuntimeException
     * @return \Procure\Domain\AccountPayable\APDoc
     */
    public static function createFrom(APSnapshot $snapshot, CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {
        $instance = new self();
        $instance->_checkParams($snapshot, $validationService, $sharedService);

        if ($options == null) {
            throw new InvalidArgumentException("Options is null");
        }

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $instance->setExchangeRate($fxRate);

        $instance->validateHeader($validationService->getHeaderValidators());

        if ($instance->hasErrors()) {
            throw new \RangeException($instance->getNotification()->errorMessage());
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
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($instance, false);

        if ($rootSnapshot == null) {
            throw new \RuntimeException(sprintf("Error orcured when creating AP #%s", $instance->getId()));
        }

        $instance->id = $rootSnapshot->getId();

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetDocVersion($rootSnapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new ApHeaderCreated($target, $defaultParams, $params);
        $instance->addEvent($event);
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
    public static function updateFrom(APSnapshot $snapshot, CommandOptions $options, $params, ValidationServiceInterface $validationService, SharedService $sharedService)
    {
        $instance = new self();
        $instance->_checkParams($snapshot, $validationService, $sharedService);

        if ($options == null) {
            throw new InvalidArgumentException("Opptions is null");
        }

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $instance->setExchangeRate($fxRate);

        $instance->validateHeader($validationService->getHeaderValidators());

        if ($instance->hasErrors()) {
            throw new \RangeException(sprintf("%s-%s", $instance->getNotification()->errorMessage(), __FUNCTION__));
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
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rootSnapshot = $rep->storeHeader($instance, false);

        if ($rootSnapshot == null) {
            throw new \RuntimeException(sprintf("%s-%s", "Error orcured when creating AP!", __FUNCTION__));
        }

        $instance->id = $rootSnapshot->getId();

        $target = $rootSnapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($rootSnapshot->getId());
        $defaultParams->setTargetToken($rootSnapshot->getToken());
        $defaultParams->setTargetDocVersion($rootSnapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($rootSnapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;

        $event = new ApHeaderUpdated($target, $defaultParams, $params);
        $instance->addEvent($event);

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
            $snapshot->uuid = Uuid::uuid4()->toString();
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
    protected function doPost(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
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

        $this->validate($validationService->getHeaderValidators(), $validationService->getRowValidators(), true);

        if ($this->hasErrors()) {
            throw new \RuntimeException(sprintf("%s-%s", $this->getNotification()->errorMessage(), __FUNCTION__));
        }

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rep->post($this, true);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\AccountPayable\GenericAP::doReverse()
     */
    protected function doReverse(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
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

        $this->validate($validationService->getHeaderValidators(), $validationService->getRowValidators(), true);

        if ($this->hasErrors()) {
            throw new \RuntimeException(sprintf("%s-%s", $this->getNotification()->errorMessage(), __FUNCTION__));
        }

        $rep = $sharedService->getPostingService()->getCmdRepository();
        $rep->post($this, true);
    }

    protected function afterPost(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    protected function prePost(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    protected function preReserve(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    protected function afterReserve(CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {}

    protected function raiseEvent()
    {}

    // =============================

    /**
     *
     * @param APSnapshot $snapshot
     * @param ValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     */
    private function _checkParams(APSnapshot $snapshot, ValidationServiceInterface $validationService, SharedService $sharedService)
    {
        if (! $snapshot instanceof APSnapshot) {
            throw new InvalidArgumentException("APSnapshot not found!");
        }

        if ($validationService == null) {
            throw new InvalidArgumentException("Validation service not found!");
        }

        if (empty($validationService->getHeaderValidators())) {
            throw new InvalidArgumentException("HeaderValidatorCollection not found");
        }
        if ($sharedService == null) {
            throw new InvalidArgumentException("SharedService service not found");
        }
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
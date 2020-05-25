<?php
namespace Procure\Domain\GoodsReceipt;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\SimpleCollection;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\AccountPayable\APRow;
use Procure\Domain\Event\Gr\GrHeaderCreated;
use Procure\Domain\Event\Gr\GrHeaderUpdated;
use Procure\Domain\Event\Gr\GrPosted;
use Procure\Domain\Event\Gr\GrReversed;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\InvalidOperationException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Exception\PoInvalidArgumentException;
use Procure\Domain\Exception\ValidationFailedException;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\Service\GrPostingService;
use Procure\Domain\Service\SharedService;
use Procure\Domain\Service\Contracts\ValidationServiceInterface;
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
class GRDoc extends GenericGR
{

    private static $instance = null;

    // Specific Attribute
    // ===================
    protected $reversalDoc;

    protected $targetWhList;

    protected $targetDepartmentList;

    /**
     *
     * @return mixed
     */
    public function getTargetDepartmentList()
    {
        return $this->targetDepartmentList;
    }

    // Specific Attribute
    // ===================

    /**
     *
     * @param \Procure\Domain\GoodsReceipt\GRDoc $instance
     */
    protected static function setInstance($instance)
    {
        GRDoc::$instance = $instance;
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
     * @param mixed $targetWhList
     */
    protected function setTargetWhList($targetWhList)
    {
        $this->targetWhList = $targetWhList;
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
    public function getTargetWhList()
    {
        return $this->targetWhList;
    }

    // =====================
    public function slipByWarehouse()
    {
        $results = [];

        if ($this->getTargetWhList() == null) {
            return null;
        }
        if (count($this->getTargetWhList() > 1)) {
            foreach ($this->getTargetWhList() as $wh) {
                $doc = new self();
            }
        }
    }

    public function slipByDepartment()
    {
        $results = new SimpleCollection();

        if ($this->getTargetDepartmentList() == null) {
            return null;
        }

        foreach ($this->getDocRows() as $row) {
            /**
             *
             * @var GRRow $row ;
             */
            $dept = $row->getPrDepartment();

            if ($dept == null) {
                $dept = - 1;
            }
            $results->addChild($dept, $row);
        }

        return $results;
    }

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
     * @param GRSnapshot $snapshot
     * @return void|\Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function makeFromSnapshot(GRSnapshot $snapshot)
    {
        if (! $snapshot instanceof GRSnapshot)
            return;

        if ($snapshot->uuid == null) {
            $snapshot->uuid = Ramsey\Uuid\Uuid::uuid4()->toString();
        }

        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * @param PODoc $sourceObj
     * @throws InvalidArgumentException
     * @return \Procure\Domain\GoodsReceipt\GRDoc
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

        if ($sourceObj->getTransactionStatus() == \Procure\Domain\Shared\Constants::TRANSACTION_STATUS_COMPLETED) {
            throw new InvalidArgumentException("GR is completed!");
        }

        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }
        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRDoc $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        // overwrite.
        $instance->setDocType(\Procure\Domain\Shared\Constants::PROCURE_DOC_TYPE_GR_FROM_PO); // important.

        $createdBy = $options->getUserId();
        $createdDate = new \DateTime();
        $instance->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
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

            $grRow = GrRow::createFromPoRow($r, $options);
            // echo sprintf("\n %s, PoRowId %s, %s" , $grRow->getItemName(), $grRow->getPoRow(), $grRow->getPrRow());
            $instance->addRow($grRow);

            $instance->validateRow($grRow, $rowValidators);
        }
        return $instance;
    }

    /**
     *
     * @param APDoc $sourceObj
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @throws InvalidArgumentException
     * @throws InvalidArgumentException
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function copyFromAP(APDoc $sourceObj, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators)
    {
        if (! $sourceObj instanceof APDoc) {
            throw new InvalidArgumentException("AP Entity is required");
        }

        $rows = $sourceObj->getDocRows();

        if ($rows == null) {
            throw new InvalidArgumentException("AP Entity is empty!");
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        if ($sourceObj->getDocStatus() !== ProcureDocStatus::DOC_STATUS_POSTED) {
            throw new InvalidOperationException("AP document is not posted!");
        }

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRDoc $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        // overwrite.
        $instance->setDocType(Constants::PROCURE_DOC_TYPE_GR_FROM_INVOICE); // important.

        $createdBy = $options->getUserId();
        $createdDate = new \DateTime();
        $instance->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
        $instance->validateHeader($headerValidators);

        foreach ($rows as $r) {

            /**
             *
             * @var APRow $r ;
             */

            $grRow = GrRow::copyFromApRow($r, $options);
            // echo sprintf("\n %s, PoRowId %s, %s" , $grRow->getItemName(), $grRow->getPoRow(), $grRow->getPrRow());
            $instance->addRow($grRow);

            $instance->validateRow($grRow, $rowValidators);
        }
        return $instance;
    }

    /**
     *
     * @param APDoc $sourceObj
     * @param CommandOptions $options
     * @param ValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     * @throws ValidationFailedException
     * @throws OperationFailedException
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function postCopyFromAP(APDoc $sourceObj, CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {
        if (! $sourceObj instanceof APDoc) {
            throw new InvalidArgumentException("AP Entity is required");
        }

        $rows = $sourceObj->getDocRows();

        if ($rows == null) {
            throw new InvalidArgumentException("AP Entity is empty!");
        }

        if ($sourceObj->getDocStatus() !== ProcureDocStatus::DOC_STATUS_POSTED) {
            throw new InvalidArgumentException("AP document is not posted yet!");
        }

        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRDoc $instance
         */
        $instance = new self();

        $instance->_checkParams($validationService, $sharedService);

        // also row validation needed.
        if ($validationService->getRowValidators() == null) {
            throw new InvalidArgumentException("Rows validators not found");
        }

        $instance = $sourceObj->convertTo($instance);

        $createdBy = $options->getUserId();
        $createdDate = new \DateTime();
        $instance->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        // overwrite.
        $instance->setDocType(Constants::PROCURE_DOC_TYPE_GR_FROM_INVOICE); // important.
        $instance->markAsPosted($createdBy, $sourceObj->getPostingDate());

        foreach ($rows as $r) {

            /**
             *
             * @var APRow $r ;
             */

            $grRow = GrRow::copyFromApRow($instance, $r, $options);
            $grRow->markAsPosted($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
            $instance->addRow($grRow);
        }

        $instance->setInvoiceNo($sourceObj->getDocNumber());
        $instance->setRemarks(\sprintf("Auto. generated from AP %s", $sourceObj->getId()));

        $instance->validate($validationService->getHeaderValidators(), $validationService->getRowValidators());

        if ($instance->hasErrors()) {
            throw new ValidationFailedException($instance->getErrorMessage());
        }

        $instance->clearEvents();

        $snapshot = $sharedService->getPostingService()
            ->getCmdRepository()
            ->post($instance, true);

        if (! $snapshot instanceof GRSnapshot) {
            throw new OperationFailedException(sprintf("Error orcured when creating GR #%s", $instance->getId()));
        }

        $instance->setId($snapshot->getId());
        $instance->setToken($snapshot->getToken());

        $target = $snapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($snapshot->getId());
        $defaultParams->setTargetToken($snapshot->getToken());
        $defaultParams->setTargetDocVersion($snapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($snapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;
        $event = new GrPosted($target, $defaultParams, $params);
        $instance->addEvent($event);

        return $instance;
    }

    /**
     *
     * @param APDoc $sourceObj
     * @param CommandOptions $options
     * @param ValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     * @throws \RuntimeException
     * @throws ValidationFailedException
     * @throws OperationFailedException
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function postCopyFromAPRerveral(APDoc $sourceObj, CommandOptions $options, ValidationServiceInterface $validationService, SharedService $sharedService)
    {
        if (! $sourceObj instanceof APDoc) {
            throw new InvalidArgumentException("AP Entity is required");
        }

        $rows = $sourceObj->getDocRows();

        if ($rows == null) {
            throw new InvalidArgumentException("AP Entity is empty!");
        }

        if ($sourceObj->getDocStatus() !== ProcureDocStatus::DOC_STATUS_REVERSED) {
            throw new \RuntimeException(\sprintf("AP document status is not valid for this operation! %s", $sourceObj->getDocStatus()));
        }

        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRDoc $instance
         */
        $instance = new self();
        $instance->_checkParams($validationService, $sharedService);
        // also row validation needed.
        if ($validationService->getRowValidators() == null) {
            throw new InvalidArgumentException("Rows validators not found");
        }

        $instance = $sourceObj->convertTo($instance);

        $createdBy = $options->getUserId();
        $createdDate = new \DateTime();
        $instance->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        // overwrite.
        $instance->setDocType(Constants::PROCURE_DOC_TYPE_GR_FROM_INVOICE); // important.
        $instance->markAsReversed($createdBy, $sourceObj->getReversalDate());

        foreach ($rows as $r) {

            /**
             *
             * @var APRow $r ;
             */

            $grRow = GrRow::copyFromApRow($instance, $r, $options);
            $grRow->markAsReversed($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
            $instance->addRow($grRow);
        }
        $instance->setInvoiceNo($sourceObj->getDocNumber());
        $instance->setRemarks(\sprintf("Auto. generated from AP Reversal %s", $sourceObj->getId()));

        $instance->validate($validationService->getHeaderValidators(), $validationService->getRowValidators());

        if ($instance->hasErrors()) {
            throw new ValidationFailedException($instance->getErrorMessage());
        }

        $instance->clearEvents();

        $snapshot = $sharedService->getPostingService()
            ->getCmdRepository()
            ->post($instance, true);

        if (! $snapshot instanceof GRSnapshot) {
            throw new OperationFailedException(sprintf("Error orcured when creating GR from AP reversal #%s", $sourceObj->getId()));
        }

        $target = $snapshot;
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($snapshot->getId());
        $defaultParams->setTargetToken($snapshot->getToken());
        $defaultParams->setTargetDocVersion($snapshot->getDocVersion());
        $defaultParams->setTargetRrevisionNo($snapshot->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());
        $params = null;
        $event = new GrReversed($target, $defaultParams, $params);
        $instance->addEvent($event);

        $instance->setId($snapshot->getId());
        $instance->setToken($snapshot->getToken());
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
     * @throws InvalidArgumentException
     * @throws InvalidArgumentException
     * @throws OperationFailedException
     */
    public function saveFromPO(GRSnapshot $snapshot, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService)
    {
        if (! $this->getDocStatus() == GRDocStatus::DOC_STATUS_DRAFT) {
            throw new InvalidArgumentException(sprintf("PO is already posted/closed or being amended! %s", $this->getId()));
        }

        if ($this->getDocRows() == null) {
            throw new InvalidArgumentException(sprintf("Documment is empty! %s", $this->getId()));
        }

        if (! $this->getDocType() == Constants::PROCURE_DOC_TYPE_GR_FROM_PO) {
            throw new InvalidArgumentException(sprintf("Doctype is not vadid! %s", $this->getDocType()));
        }

        $this->_checkInputParams($snapshot, $headerValidators, $sharedService, $postingService);

        if ($rowValidators == null) {
            throw new InvalidArgumentException("HeaderValidatorCollection not found");
        }

        if ($options == null) {
            throw new InvalidArgumentException("Comnand Options not found!");
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
            throw new OperationFailedException($this->getErrorMessage());
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
     * @throws InvalidArgumentException
     * @throws OperationFailedException
     * @throws OperationFailedException
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function createFrom(GRSnapshot $snapshot, CommandOptions $options, HeaderValidatorCollection $headerValidators, SharedService $sharedService, GrPostingService $postingService)
    {
        $instance = new self();
        $instance->_checkInputParams($snapshot, $headerValidators, $sharedService, $postingService);

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $instance->setExchangeRate($fxRate);

        $instance->validateHeader($headerValidators);

        if ($instance->hasErrors()) {
            throw new OperationFailedException($instance->getNotification()->errorMessage());
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
            throw new OperationFailedException(sprintf("Error orcured when creating PO #%s", $instance->getId()));
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

        $event = new GrHeaderCreated($target, $defaultParams, $params);
        $instance->addEvent($event);

        return $instance;
    }

    /**
     *
     * @param GrSnapshot $snapshot
     * @param CommandOptions $options
     * @param array $params
     * @param HeaderValidatorCollection $headerValidators
     * @param SharedService $sharedService
     * @param GrPostingService $postingService
     * @throws PoInvalidArgumentException
     * @throws OperationFailedException
     * @throws OperationFailedException
     * @return \Procure\Domain\GoodsReceipt\GRDoc
     */
    public static function updateFrom(GrSnapshot $snapshot, CommandOptions $options, $params, HeaderValidatorCollection $headerValidators, SharedService $sharedService, GrPostingService $postingService)
    {
        $instance = new self();
        $instance->_checkInputParams($snapshot, $headerValidators, $sharedService, $postingService);

        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $fxRate = $sharedService->getFxService()->checkAndReturnFX($snapshot->getDocCurrency(), $snapshot->getLocalCurrency(), $snapshot->getExchangeRate());
        $instance->setExchangeRate($fxRate);

        $instance->validateHeader($headerValidators);

        if ($instance->hasErrors()) {
            throw new OperationFailedException($instance->getNotification()->errorMessage());
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
            throw new OperationFailedException(sprintf("Error orcured when creating PO #%s", $instance->getId()));
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

        $event = new GrHeaderUpdated($target, $defaultParams, $params);

        $instance->addEvent($event);
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

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\GenericGR::doPost()
     */
    protected function doPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService)
    {
        /**
         *
         * @var GRRow $row ;
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
            throw new OperationFailedException($this->getNotification()->errorMessage());
        }

        $postingService->getCmdRepository()->post($this, true);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\GenericGR::doReverse()
     */
    protected function doReverse(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService)
    {
        /**
         *
         * @var GRRow $row ;
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
            throw new OperationFailedException($this->getNotification()->errorMessage());
        }

        $postingService->getCmdRepository()->post($this, false);
    }

    /**
     *
     * @param GrSnapshot $snapshot
     * @param HeaderValidatorCollection $headerValidators
     * @param SharedService $sharedService
     * @param GrPostingService $postingService
     * @throws InvalidArgumentException
     */
    private function _checkInputParams(GrSnapshot $snapshot, HeaderValidatorCollection $headerValidators, SharedService $sharedService, GrPostingService $postingService)
    {
        if (! $snapshot instanceof GrSnapshot) {
            throw new InvalidArgumentException("GR snapshot not found!");
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

    /**
     *
     * @param ValidationServiceInterface $validationService
     * @param SharedService $sharedService
     * @throws InvalidArgumentException
     */
    private function _checkParams(ValidationServiceInterface $validationService, SharedService $sharedService)
    {
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

    protected function afterPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService)
    {}

    protected function prePost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService)
    {}

    protected function preReserve(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService)
    {}

    protected function afterReserve(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, GrPostingService $postingService)
    {}

    protected function raiseEvent()
    {}
}
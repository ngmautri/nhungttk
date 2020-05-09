<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Application\Domain\Util\SimpleCollection;
use Procure\Application\Command\PO\Options\PoPostOptions;
use Procure\Domain\Event\Po\PoHeaderCreated;
use Procure\Domain\Event\Po\PoHeaderUpdated;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\InvalidOperationException;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Exception\PoCreateException;
use Procure\Domain\Exception\PoInvalidArgumentException;
use Procure\Domain\Exception\PoUpdateException;
use Procure\Domain\Exception\ValidationFailedException;
use Procure\Domain\QuotationRequest\QRDoc;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\POSpecService;
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
class PODoc extends GenericPO
{

    // Addtional attribute.
    private static $instance = null;

    private $grCollection;

    private $apCollection;

    /**
     *
     * @param QRDoc $sourceObj
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @throws InvalidArgumentException
     * @return \Procure\Domain\PurchaseOrder\PODoc
     */
    public static function createFromQuotation(QRDoc $sourceObj, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators)
    {
        if (! $sourceObj instanceof QRDoc) {
            throw new InvalidArgumentException("Quotation Entity is required");
        }

        $rows = $sourceObj->getDocRows();

        if ($rows == null) {
            throw new InvalidArgumentException("Quote Entity is empty!");
        }

        if ($sourceObj->getDocStatus() !== ProcureDocStatus::DOC_STATUS_POSTED) {
            throw new InvalidArgumentException("Quote document is not posted!");
        }

        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }
        /**
         *
         * @var PODoc $instance
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        // overwrite.
        $instance->setDocType(\Procure\Domain\Shared\Constants::PROCURE_DOC_TYPE_PO_FROM_QOUTE); // important.

        $createdBy = $options->getUserId();
        $createdDate = new \DateTime();
        $instance->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));
        $instance->validateHeader($headerValidators);

        foreach ($rows as $r) {

            /**
             *
             * @var PORow $r ;
             */

            // ignore completed row;

            $localEntity = PORow::createFromQuoteRow($r, $options);
            $instance->addRow($localEntity);
            $instance->validateRow($localEntity, $rowValidators);
        }
        return $instance;
    }

    /**
     *
     * @param POSnapshot $snapshot
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param RowValidatorCollection $rowValidators
     * @param SharedService $sharedService
     * @param POPostingService $postingService
     * @throws InvalidOperationException
     * @throws InvalidArgumentException
     * @throws ValidationFailedException
     * @throws OperationFailedException
     * @return \Procure\Domain\AccountPayable\APSnapshot
     */
    public function saveFromQuotation(POSnapshot $snapshot, CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {
        if (! $this->getDocStatus() == ProcureDocStatus::DOC_STATUS_DRAFT) {
            throw new InvalidOperationException(sprintf("PO is already posted/closed or being amended! %s", __FUNCTION__));
        }

        if ($this->getDocRows() == null) {
            throw new InvalidOperationException(sprintf("Documment is empty! %s", __FUNCTION__));
        }

        if (! $this->getDocType() == Constants::PROCURE_DOC_TYPE_PO_FROM_QOUTE) {
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

        if (! $rootSnapshot instanceof POSnapshot) {
            throw new OperationFailedException(\sprintf("Errors occured when saving PO"));
        }

        return $rootSnapshot;
    }

    /**
     *
     * @return mixed
     */
    public function getGrCollection()
    {
        if ($this->grCollection == null) {
            $this->grCollection = new SimpleCollection();
            return $this->grCollection;
        }
        return $this->grCollection;
    }

    /**
     *
     * @return mixed
     */
    public function getApCollection()
    {
        if ($this->apCollection == null) {
            $this->apCollection = new SimpleCollection();
            return $this->apCollection;
        }
        return $this->apCollection;
    }

    /**
     *
     * @param mixed $grList
     */
    public function addGrId($id)
    {
        $collection = $this->getGrCollection();
        $collection->add($id);
    }

    public function addGrArray($idArray)
    {
        if (count($idArray) == null) {
            return;
        }

        foreach ($idArray as $e) {
            $collection = $this->getGrCollection();
            $collection->add($e);
        }
    }

    public function addApArray($idArray)
    {
        if (count($idArray) == null) {
            return;
        }

        foreach ($idArray as $e) {
            $collection = $this->getApCollection();
            $collection->add($e);
        }
    }

    public function addApId($id)
    {
        $collection = $this->getApList();
        $collection->add($id);
    }

    /**
     *
     * @param mixed $apList
     */
    public function setApList($apList)
    {
        $this->apList = $apList;
    }

    public static function createSnapshotProps()
    {
        $baseClass = "Procure\Domain\PurchaseOrder\BaseDoc";
        $entity = new self();
        $reflectionClass = new \ReflectionClass($entity);

        $props = $reflectionClass->getProperties();

        foreach ($props as $property) {
            // echo $property->class . "\n";
            if ($property->class == $reflectionClass->getName() || $property->class == $baseClass) {
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

    private function __construct()
    {}

    /**
     *
     * @return \Procure\Domain\PurchaseOrder\PODoc
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new PODoc();
        }
        return self::$instance;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\GenericPO::doPost()
     */
    protected function doPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {

        /**
         *
         * @var \Procure\Domain\PurchaseOrder\PORow $row ;
         * @todo: decoup dependency
         * @var PoPostOptions $options ;
         */
        $postedDate = new \Datetime();
        $postedBy = $options->getUserId();
        $this->markAsPosted($postedBy, date_format($postedDate, 'Y-m-d H:i:s'));

        foreach ($this->getDocRows() as $row) {

            if ($row->getDocQuantity() == 0) {
                continue;
            }

            $row->markAsPosted($options->getUserId(), date_format($postedDate, 'Y-m-d H:i:s'));
        }

        $this->validate($headerValidators, $rowValidators, true);

        if ($this->hasErrors()) {
            throw new ValidationFailedException($this->getNotification()->errorMessage());
        }

        $postingService->getCmdRepository()->post($this, true);
    }

    /**
     *
     * @param POSnapshot $snapshot
     * @param array $params
     * @param HeaderValidatorCollection $headerValidators
     * @param SharedService $sharedService
     * @param POPostingService $postingService
     * @throws PoInvalidArgumentException
     * @throws PoCreateException
     * @throws PoUpdateException
     * @return \Procure\Domain\PurchaseOrder\PODoc
     */
    public static function createFrom(POSnapshot $snapshot, CommandOptions $options, HeaderValidatorCollection $headerValidators, SharedService $sharedService, POPostingService $postingService)
    {
        if (! $snapshot instanceof POSnapshot) {
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
            throw new PoCreateException($instance->getNotification()->errorMessage());
        }

        $createdDate = new \Datetime();
        $instance->setCreatedOn(date_format($createdDate, 'Y-m-d H:i:s'));
        $instance->setDocStatus(PODocStatus::DOC_STATUS_DRAFT);
        $instance->setDocType(PODocType::PO);
        $instance->setIsActive(1);
        $instance->setSysNumber(Constants::SYS_NUMBER_UNASSIGNED);
        $instance->setRevisionNo(1);
        $instance->setDocVersion(1);
        $instance->setUuid(Ramsey\Uuid\Uuid::uuid4()->toString());
        $instance->setToken($instance->getUuid());

        $instance->recordedEvents = array();

        /**
         *
         * @var POSnapshot $rootSnapshot
         */
        $rootSnapshot = $postingService->getCmdRepository()->storeHeader($instance, false);

        if ($rootSnapshot == null) {
            throw new PoUpdateException(sprintf("Error orcured when creating PO #%s", $instance->getId()));
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

        $event = new PoHeaderCreated($target, $defaultParams, $params);
        $instance->addEvent($event);
        return $instance;
    }

    /**
     *
     * @param POSnapshot $snapshot
     * @param CommandOptions $options
     * @param array $params
     * @param HeaderValidatorCollection $headerValidators
     * @param SharedService $sharedService
     * @param POPostingService $postingService
     * @throws PoInvalidArgumentException
     * @throws PoCreateException
     * @throws PoUpdateException
     * @return \Procure\Domain\PurchaseOrder\PODoc
     */
    public static function updateFrom(POSnapshot $snapshot, CommandOptions $options, $params, HeaderValidatorCollection $headerValidators, SharedService $sharedService, POPostingService $postingService)
    {
        if (! $snapshot instanceof POSnapshot) {
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
            throw new PoCreateException($instance->getNotification()->errorMessage());
        }

        $createdDate = new \Datetime();
        $instance->setLastchangeOn(date_format($createdDate, 'Y-m-d H:i:s'));

        $instance->recordedEvents = array();

        /**
         *
         * @var POSnapshot $rootSnapshot
         */
        $rootSnapshot = $postingService->getCmdRepository()->storeHeader($instance, false);

        if ($rootSnapshot == null) {
            throw new PoUpdateException(sprintf("Error orcured when creating PO #%s", $instance->getId()));
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

        $event = new PoHeaderUpdated($target, $defaultParams, $params);
        $instance->addEvent($event);

        return $instance;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GenericDoc::makeSnapshot()
     */
    public function makeSnapshot()
    {
        return SnapshotAssembler::createSnapshotFrom($this, new POSnapshot());
    }

    /**
     * This should be only call when constructing object from storage.
     *
     * @param PODetailsSnapshot $snapshot
     * @return void|\Procure\Domain\PurchaseOrder\PODoc
     */
    public static function makeFromDetailsSnapshot(PoSnapshot $snapshot)
    {
        if (! $snapshot instanceof PoSnapshot)
            return;

        if ($snapshot->uuid == null) {
            $snapshot->uuid = Ramsey\Uuid\Uuid::uuid4()->toString();
        }
        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /**
     * This should be only call when constructing object from storage.
     *
     * @param PoSnapshot $snapshot
     * @return void|\Procure\Domain\PurchaseOrder\PODoc
     */
    public static function makeFromSnapshot(PoSnapshot $snapshot)
    {
        if (! $snapshot instanceof PoSnapshot)
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
     * @deprecated
     * @param PoSnapshot $snapshot
     * @param POSpecService $specService
     * @return void|\Procure\Domain\PurchaseOrder\PODoc
     */
    public static function updateFromSnapshot(PoSnapshot $snapshot, POSpecService $specService = null)
    {
        if (! $snapshot instanceof PoSnapshot) {
            return;
        }

        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $instance->validateHeader($specService);
        if ($instance->hasErrors()) {
            throw new PoUpdateException($instance->getErrorMessage());
        }

        return $instance;
    }

    private function _checkInputParams(POSnapshot $snapshot, HeaderValidatorCollection $headerValidators, SharedService $sharedService, POPostingService $postingService)
    {
        if (! $snapshot instanceof POSnapshot) {
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
}
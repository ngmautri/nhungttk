<?php
namespace Procure\Domain\QuotationRequest;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\Event\Qr\QrHeaderCreated;
use Procure\Domain\Event\Qr\QrHeaderUpdated;
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
final class QRDoc extends GenericQR
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

    /**
     *
     * @param QRSnapshot $snapshot
     * @param CommandOptions $options
     * @param HeaderValidatorCollection $headerValidators
     * @param SharedService $sharedService
     * @param QrPostingService $postingService
     * @throws InvalidArgumentException
     * @throws ValidationFailedException
     * @throws OperationFailedException
     * @return \Procure\Domain\QuotationRequest\QRDoc
     */
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

        $instance->clearNotification();

        $instance->validateHeader($headerValidators);

        if ($instance->hasErrors()) {
            throw new ValidationFailedException($instance->getNotification()->errorMessage());
        }

        $instance->setDocType(Constants::PROCURE_DOC_TYPE_QUOTE);

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $instance->initDoc($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        $instance->clearEvents();

        /**
         *
         * @var QRSnapshot $rootSnapshot
         */
        $rootSnapshot = $postingService->getCmdRepository()->storeHeader($instance, false);

        if ($rootSnapshot == null) {
            throw new OperationFailedException(sprintf("Error orcured when creating Quote #%s", $instance->getId()));
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

        $event = new QrHeaderCreated($target, $defaultParams, $params);

        $instance->addEvent($event);
        return $instance;
    }

    /**
     *
     * @param QRSnapshot $snapshot
     * @param CommandOptions $options
     * @param array $params
     * @param HeaderValidatorCollection $headerValidators
     * @param SharedService $sharedService
     * @param QrPostingService $postingService
     * @throws InvalidArgumentException
     * @throws ValidationFailedException
     * @throws OperationFailedException
     * @return \Procure\Domain\QuotationRequest\QRDoc
     */
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
            throw new OperationFailedException(sprintf("%s-%s", "Error orcured when creating Quotation!", __FUNCTION__));
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

        $event = new QrHeaderUpdated($target, $defaultParams, $params);

        $instance->addEvent($event);
        return $instance;
    }

    /**
     *
     * @param QRSnapshot $snapshot
     * @return void|\Procure\Domain\QuotationRequest\QRDoc
     */
    public static function constructFromDetailsSnapshot(QRSnapshot $snapshot)
    {
        if (! $snapshot instanceof QRSnapshot) {
            return null;
        }

        if ($snapshot->uuid == null) {
            $snapshot->uuid = Uuid::uuid4()->toString();
        }
        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    /**
     *
     * @param QRSnapshot $snapshot
     * @return NULL|\Procure\Domain\QuotationRequest\QRDoc
     */
    public static function constructFromSnapshot(QRSnapshot $snapshot)
    {
        if (! $snapshot instanceof QRSnapshot) {
            return null;
        }

        if ($snapshot->uuid == null) {
            $snapshot->uuid = Uuid::uuid4()->toString();
        }

        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

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
        $this->clearNotification();

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
        // blank
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
}
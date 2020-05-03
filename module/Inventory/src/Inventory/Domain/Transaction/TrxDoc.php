<?php
namespace Inventory\Domain\Transaction;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Shared\Constants;
use Application\Domain\Shared\SnapshotAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Contracts\PostingServiceInterface;
use Inventory\Domain\Service\SharedService;
use Inventory\Domain\Transaction\Validator\Contracts\HeaderValidatorCollection;
use Inventory\Domain\Transaction\Validator\Contracts\RowValidatorCollection;
use Procure\Domain\Event\Qr\QrHeaderCreated;
use Procure\Domain\Event\Qr\QrHeaderUpdated;
use Procure\Domain\Exception\OperationFailedException;
use Procure\Domain\Exception\ValidationFailedException;
use Procure\Domain\Service\QrPostingService;
use Ramsey\Uuid\Uuid;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxDoc extends GenericTrx
{

    // Specific Attribute, if any
    // =========================

    // ==========================
    private static $instance = null;

    private function __construct()
    {}

    public static function createFrom(TrxSnapshot $snapshot, CommandOptions $options, HeaderValidatorCollection $headerValidators, SharedService $sharedService, PostingServiceInterface $postingService)
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
         * @var TrxSnapshot $rootSnapshot
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

    public static function updateFrom(TrxSnapshot $snapshot, CommandOptions $options, $params, HeaderValidatorCollection $headerValidators, SharedService $sharedService, PostingServiceInterface $postingService)
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
         * @var TrxSnapshot $rootSnapshot
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
     * @return \Inventory\Domain\Transaction\TrxDoc
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new TrxDoc();
        }
        return self::$instance;
    }

    public static function createSnapshotProps()
    {
        $baseClass = "Inventory\Domain\Transaction\BaseDoc";
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

    public static function constructFromSnapshot(TrxSnapshot $snapshot)
    {
        if (! $snapshot instanceof TrxSnapshot) {
            return null;
        }

        if ($snapshot->uuid == null) {
            $snapshot->uuid = Uuid::uuid4()->toString();
        }

        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
        return $instance;
    }

    protected function doReverse(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, PostingServiceInterface $postingService)
    {}

    protected function doPost(CommandOptions $options, HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, PostingServiceInterface $postingService)
    {}

    private function _checkInputParams(TrxSnapshot $snapshot, HeaderValidatorCollection $headerValidators, SharedService $sharedService, QrPostingService $postingService)
    {
        if (! $snapshot instanceof TrxSnapshot) {
            throw new InvalidArgumentException("TrxSnapshot not found!");
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
}
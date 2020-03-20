<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Domain\Shared\Constants;
use Application\Domain\Shared\SnapshotAssembler;
use Procure\Domain\Event\Po\PoHeaderCreated;
use Procure\Domain\Exception\PoCreateException;
use Procure\Domain\Exception\PoInvalidArgumentException;
use Procure\Domain\Exception\PoUpdateException;
use Procure\Domain\PurchaseOrder\Validator\HeaderValidatorCollection;
use Procure\Domain\PurchaseOrder\Validator\RowValidatorCollection;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\POSpecService;
use Procure\Domain\Service\SharedService;
use Ramsey;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Application\Command\PO\Options\PoCreateOptions;
use Procure\Domain\Event\Po\PoHeaderUpdated;
use Procure\Application\Command\PO\Options\PoUpdateOptions;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PODoc extends GenericPO
{

    private function __construct()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\GenericPO::doPost()
     */
    protected function doPost(HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {
        $postedDate = new \Datetime();
        $this->setDocStatus(PODocStatus::DOC_STATUS_POSTED);
        $this->setLastchangeOn(date_format($postedDate, 'Y-m-d H:i:s'));

        foreach ($this->docRows as $row) {

            /** @var \Procure\Domain\PurchaseOrder\PORow $row ; */
            if ($row->quantity() == 0) {
                continute;
            }

            $row->setAsPosted($postedBy, $postedDate);
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

        $trigger = null;
        $params = null;
        if ($options instanceof PoCreateOptions) {
            $trigger = $options->getTriggeredBy();
            $params = [];
        }

        $instance->addEvent(new PoHeaderCreated($rootSnapshot, $trigger, $params));
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

        $trigger = null;
        if ($options instanceof PoUpdateOptions) {
            $trigger = $options->getTriggeredBy();
        }

        $instance->addEvent(new PoHeaderUpdated($rootSnapshot, $trigger, $params));
        return $instance;
    }

    /**
     *
     * @param PODetailsSnapshot $snapshot
     * @return void|\Procure\Domain\PurchaseOrder\PODoc
     */
    public static function makeFromDetailsSnapshot(PODetailsSnapshot $snapshot)
    {
        if (! $snapshot instanceof PODetailsSnapshot)
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

    protected function afterPost(HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {}

    protected function doReverse(HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {}

    protected function prePost(HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {}

    protected function preReserve(HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {}

    protected function afterReserve(HeaderValidatorCollection $headerValidators, RowValidatorCollection $rowValidators, SharedService $sharedService, POPostingService $postingService)
    {}

    protected function raiseEvent()
    {}

  
}
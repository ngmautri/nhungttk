<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Notification;
use Application\Domain\Shared\SnapshotAssembler;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\POSpecificationService;
use Ramsey;
use Procure\Domain\Exception\PoUpdateException;
use Procure\Domain\Event\POHeaderUpdatedEvent;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PODoc extends GenericPO

{

    protected function afterPost(POSpecificationService $specificationService, POPostingService $postingService, Notification $notification = null)
    {}

    protected function doReverse(POSpecificationService $specificationService, POPostingService $postingService, Notification $notification = null)
    {}

    protected function prePost(POSpecificationService $specificationService, POPostingService $postingService, Notification $notification = null)
    {}

    protected function preReserve(POSpecificationService $specificationService, POPostingService $postingService, Notification $notification = null)
    {}

    protected function specificHeaderValidation(POSpecificationService $specificationService, Notification $notification, $isPosting = false)
    {}

    protected function specificValidation(POSpecificationService $specificationService, Notification $notification, $isPosting = false)
    {}

    protected function afterReserve(POSpecificationService $specificationService, POPostingService $postingService, Notification $notification = null)
    {}

    protected function raiseEvent()
    {}

    protected function doPost(POSpecificationService $specificationService, POPostingService $postingService, Notification $notification = null)
    {}

    private function __construct()
    {}

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
     * @param POSpecificationService $specificationService
     * @return void|\Procure\Domain\PurchaseOrder\PODoc
     */
    public static function updateFromSnapshot(PoSnapshot $snapshot, POSpecificationService $specificationService = null)
    {
        if (! $snapshot instanceof PoSnapshot) {
            return;
        }

        $instance = new self();
        SnapshotAssembler::makeFromSnapshot($instance, $snapshot);

        $notification = $instance->validateHeader($specificationService);
        if ($notification->hasErrors()) {
            throw new PoUpdateException($notification->errorMessage());
        }
        $instance->registerEvent(new POHeaderUpdatedEvent());
        
        return $instance;
    }

    protected function specificRowValidation(PORow $row, POSpecificationService $specificationService, Notification $notification, $isPosting = false)
    {}
}
<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Domain\Shared\SnapshotAssembler;
use Procure\Domain\Event\POHeaderUpdatedEvent;
use Procure\Domain\Exception\PoUpdateException;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\POSpecService;
use Ramsey;

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

        $notification = $instance->validateHeader($specService);
        if ($notification->hasErrors()) {
            throw new PoUpdateException($notification->errorMessage());
        }
        $instance->registerEvent(new POHeaderUpdatedEvent());

        return $instance;
    }

    protected function afterPost(POSpecService $specService, POPostingService $postingService)
    {}

    protected function doReverse(POSpecService $specService, POPostingService $postingService)
    {}

    protected function prePost(POSpecService $specService, POPostingService $postingService)
    {}

    protected function preReserve(POSpecService $specService, POPostingService $postingService)
    {}

    protected function specificHeaderValidation(POSpecService $specService, $isPosting = false)
    {}

    protected function specificValidation(POSpecService $specService, $isPosting = false)
    {}

    protected function afterReserve(POSpecService $specService, POPostingService $postingService)
    {}

    protected function raiseEvent()
    {}

    protected function doPost(POSpecService $specService, POPostingService $postingService)
    {}

    protected function specificRowValidation(PORow $row, POSpecService $specService, $isPosting = false)
    {}
}
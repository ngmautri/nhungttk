<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Notification;
use Application\Domain\Shared\SnapshotAssembler;
use Procure\Domain\Service\POPostingService;
use Procure\Domain\Service\POSpecificationService;

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
            
            $instance = new self();
            SnapshotAssembler::makeFromSnapshot($instance, $snapshot);
            return $instance;
    }
    protected function specificRowValidation(PORow $row, POSpecificationService $specificationService, Notification $notification, $isPosting = false)
    {}

}
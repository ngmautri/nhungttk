<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Domain\Shared\SnapshotAssembler;
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

        $instance->validateHeader($specService);
        if ($instance->hasErrors()) {
            throw new PoUpdateException($instance->getErrorMessage());
        }

        return $instance;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\GenericPO::doPost()
     */
    protected function doPost(POSpecService $specService, POPostingService $postingService)
    {
        $this->docType = "";
        $this->docStatus = PODocStatus::DOC_STATUS_POSTED;
        $this->revisionNo = $this->revisionNo + 1;
        $this->lastchangeBy = null;
        $this->lastchangeOn = new \DateTime();

        $n = 0;
        foreach ($this->docRows as $r) {

            /** @var \Procure\Domain\PurchaseOrder\PORow $r ; */
            $snapshot = $r->makeSnapshot();

            /**
             * Double check only.
             * Receipt of ZERO quantity not allowed
             */
            if ($snapshot->quantity() == 0) {
                continute;
            }

            $n ++;
            $snapshot->isPosted = 1;
            $snapshot->isDraft = 0;
            $snapshot->docStatus = $this->docStatus;
            $snapshot->rowNumber = $n;
            $snapshot->lastchangeOn = $this->lastchangeOn;
        }

        $postingService->getCmdRepository()->post($this, true);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\GenericPO::raiseEvent()
     */
    protected function raiseEvent()
    {}

    protected function afterPost(POSpecService $specService, POPostingService $postingService)
    {}

    protected function doReverse(POSpecService $specService, POPostingService $postingService)
    {}

    protected function prePost(POSpecService $specService, POPostingService $postingService)
    {}

    protected function preReserve(POSpecService $specService, POPostingService $postingService)
    {}
  
    protected function afterReserve(POSpecService $specService, POPostingService $postingService)
    {}

}
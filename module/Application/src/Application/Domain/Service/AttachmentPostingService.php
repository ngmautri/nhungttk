<?php
namespace Application\Domain\Service;

use Application\Notification;
use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use Inventory\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\GenericPO;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Domain\PurchaseOrder\GenericAttachment;

/**
 * PO Specification Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AttachmentSpecService
{

    protected $cmdRepository;

    protected $procureQueryRepositoryFactory;

    protected $inventoryQueryRepository;

    public function __construct(POCmdRepositoryInterface $cmdRepository, QueryRepositoryFactoryInteface $procureQueryRepositoryFactory, QueryRepositoryFactoryInteface $inventoryQueryRepository)
    {
        if ($cmdRepository == null) {
            throw new InvalidArgumentException("PO cmd repository not set!");
        }

        if ($procureQueryRepositoryFactory == null) {
            throw new InvalidArgumentException("procure query repository not set!");
        }

        if ($inventoryQueryRepository == null) {
            throw new InvalidArgumentException("Inventory query repository not set!");
        }

        $this->cmdRepository = $cmdRepository;
        $this->procureQueryRepositoryFactory = $procureQueryRepositoryFactory;
        $this->inventoryQueryRepository = $procureQueryRepositoryFactory;
    }

    /**
     *
     * @return \Procure\Domain\PurchaseOrder\POCmdRepositoryInterface
     */
    public function getCmdRepository()
    {
        return $this->cmdRepository;
    }

    /**
     *
     * @return \Inventory\Domain\Service\QueryRepositoryFactoryInteface
     */
    public function getProcureQueryRepositoryFactory()
    {
        return $this->procureQueryRepositoryFactory;
    }

    /**
     *
     * @return \Inventory\Domain\Service\QueryRepositoryFactoryInteface
     */
    public function getInventoryQueryRepository()
    {
        return $this->inventoryQueryRepository;
    }
}

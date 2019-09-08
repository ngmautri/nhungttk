<?php
namespace Procure\Domain\Service;

use Inventory\Domain\Service\QueryRepositoryFactoryInteface;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\POCmdRepositoryInterface;

/**
 * Transaction Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class POPostingService
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
     * @return \Procure\Domain\PurchaseOrder\POCmdRepositoryInterface
     */
    public function getCmdRepository()
    {
        return $this->cmdRepository;
    }

    /**
     * @return \Inventory\Domain\Service\QueryRepositoryFactoryInteface
     */
    public function getProcureQueryRepositoryFactory()
    {
        return $this->procureQueryRepositoryFactory;
    }

    /**
     * @return \Inventory\Domain\Service\QueryRepositoryFactoryInteface
     */
    public function getInventoryQueryRepository()
    {
        return $this->inventoryQueryRepository;
    }

    
  
}

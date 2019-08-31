<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Domain\PurchaseOrder\GenericPO;
use Procure\Domain\PurchaseOrder\POCmdRepositoryInterface;
use Procure\Domain\PurchaseOrder\PORow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrinePODocCmdRepository extends AbstractDoctrineRepository implements POCmdRepositoryInterface
{
    public function storeRow(GenericPO $rootEntity, PORow $row, $isPosting = false)
    {}

    public function post(GenericPO $rootEntity, $generateSysNumber = True)
    {}

    public function createRow($poId, GenericPO $row, $isPosting = false)
    {}

    public function storeHeader(GenericPO $rootEntity, $generateSysNumber = false, $isPosting = false)
    {}

    public function store(GenericPO $rootEntity, $generateSysNumber = false, $isPosting = false)
    {}

    
    
    

      
}

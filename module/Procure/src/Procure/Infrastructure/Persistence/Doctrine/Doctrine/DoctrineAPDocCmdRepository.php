<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Domain\APInvoice\APDocCmdRepositoryInterface;
use Procure\Domain\APInvoice\APDocRow;
use Procure\Domain\APInvoice\GenericAPDoc;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrineAPDocCmdRepository extends AbstractDoctrineRepository implements APDocCmdRepositoryInterface
{
    
    
    public function storeRow(GenericAPDoc $rootEntity, APDocRow $row, $isPosting = false)
    {}

    public function post(GenericAPDoc $rootEntity, $generateSysNumber = True)
    {}

    public function createRow($invId, GenericAPDoc $row, $isPosting = false)
    {}

    public function storeHeader(GenericAPDoc $rootEntity, $generateSysNumber = false, $isPosting = false)
    {}

    public function store(GenericAPDoc $rootEntity, $generateSysNumber = false, $isPosting = false)
    {}

      
}

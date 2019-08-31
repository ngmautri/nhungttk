<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Domain\PurchaseRequest\GenericPR;
use Procure\Domain\PurchaseRequest\PRCmdRepositoryInterface;
use Procure\Domain\PurchaseRequest\PRRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DoctrinePRCmdRepository extends AbstractDoctrineRepository implements PRCmdRepositoryInterface
{

    public function storeRow(GenericPR $rootEntity, PRRow $row, $isPosting = false)
    {}

    public function post(GenericPR $rootEntity, $generateSysNumber = True)
    {}

    public function createRow($prId, GenericPR $row, $isPosting = false)
    {}

    public function storeHeader(GenericPR $rootEntity, $generateSysNumber = false, $isPosting = false)
    {}

    public function store(GenericPR $rootEntity, $generateSysNumber = false, $isPosting = false)
    {}
}

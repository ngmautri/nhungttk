<?php
namespace Procure\Domain\GoodsReceipt\Repository;

use Procure\Domain\PurchaseOrder\GenericPO;
use Procure\Domain\PurchaseOrder\PORow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface GrCmdRepositoryInterface
{

    public function store(GenericPO $rootEntity, $generateSysNumber = false, $isPosting = false);

    public function post(GenericPO $rootEntity, $generateSysNumber = True);

    public function storeHeader(GenericPO $rootEntity, $generateSysNumber = false, $isPosting = false);

    public function storeRow(GenericPO $rootEntity, PORow $localEntity, $isPosting = false);

    public function createRow($poId, PORow $localEntity, $isPosting = false);
}

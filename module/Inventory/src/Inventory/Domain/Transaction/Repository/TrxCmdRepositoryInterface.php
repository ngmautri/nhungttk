<?php
namespace Inventory\Domain\Transaction\Repository;

use Inventory\Domain\Transaction\BaseRow;
use Inventory\Domain\Transaction\GenericTrx;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface TrxCmdRepositoryInterface
{

    public function store(GenericTrx $rootEntity, $generateSysNumber = false, $isPosting = false);

    public function post(GenericTrx $rootEntity, $generateSysNumber = True);

    public function storeHeader(GenericTrx $rootEntity, $generateSysNumber = false, $isPosting = false);

    public function storeRow(GenericTrx $rootEntity, BaseRow $localEntity, $isPosting = false);

    public function closeTrxOfItem($itemId);

    public function closeTrxOf($itemIds);

    public function closeWhTrxOf($warehouseId, $itemIds);

    public function closeOtherWarehouseTrx(GenericTrx $rootEntity, $itemIds);
}

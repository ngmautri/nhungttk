<?php
namespace Procure\Domain\PurchaseOrder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface POCmdRepositoryInterface
{

    public function store(GenericPO $rootEntity, $generateSysNumber = false, $isPosting = false);

    public function post(GenericPO $rootEntity, $generateSysNumber = True);

    public function storeHeader(GenericPO $rootEntity, $generateSysNumber = false, $isPosting = false);

    public function storeRow(GenericPO $rootEntity, PORow $row, $isPosting = false);

    public function createRow($poId, GenericPO $row, $isPosting = false);
}

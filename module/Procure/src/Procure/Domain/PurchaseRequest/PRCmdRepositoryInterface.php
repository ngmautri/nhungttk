<?php
namespace Procure\Domain\PurchaseRequest;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface PRCmdRepositoryInterface
{

    public function store(GenericPR $rootEntity, $generateSysNumber = false, $isPosting = false);

    public function post(GenericPR $rootEntity, $generateSysNumber = True);

    public function storeHeader(GenericPR $rootEntity, $generateSysNumber = false, $isPosting = false);

    public function storeRow(GenericPR $rootEntity, PRRow $row, $isPosting = false);

    public function createRow($prId, GenericPR $row, $isPosting = false);
}

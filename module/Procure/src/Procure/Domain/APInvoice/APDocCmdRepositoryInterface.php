<?php
namespace Procure\Domain\APInvoice;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface APDocCmdRepositoryInterface
{

    public function store(GenericAPDoc $rootEntity, $generateSysNumber = false, $isPosting = false);

    public function post(GenericAPDoc $rootEntity, $generateSysNumber = True);

    public function storeHeader(GenericAPDoc $rootEntity, $generateSysNumber = false, $isPosting = false);

    public function storeRow(GenericAPDoc $rootEntity, APDocRow $row, $isPosting = false);
    
    public function createRow($invId, GenericAPDoc $row, $isPosting = false);
    
}

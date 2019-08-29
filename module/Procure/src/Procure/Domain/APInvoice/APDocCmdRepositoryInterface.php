<?php
namespace Procure\Domain\APInvoice;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface APDocCmdRepositoryInterface
{

    public function store(GenericAPDoc $inv, $generateSysNumber = false, $isPosting = false);

    public function post(GenericAPDoc $inv, $generateSysNumber = True);

    public function storeHeader(GenericAPDoc $inv, $generateSysNumber = false, $isPosting = false);

    public function storeRow(GenericAPDoc $inv, APDocRow $row, $isPosting = false);
    
    public function createRow($invId, GenericAPDoc $row, $isPosting = false);
    
}

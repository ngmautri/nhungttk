<?php
namespace Procure\Domain\APInvoice;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface APInvoiceCmdRepositoryInterface
{

    public function store(GenericAPInvoice $inv, $generateSysNumber = false, $isPosting = false);

    public function post(GenericAPInvoice $inv, $generateSysNumber = True);

    public function storeHeader(GenericAPInvoice $inv, $generateSysNumber = false, $isPosting = false);

    public function storeRow(GenericAPInvoice $inv, APInvoiceRow $row, $isPosting = false);
    
    public function createRow($invId, APInvoiceRow $row, $isPosting = false);
    
}

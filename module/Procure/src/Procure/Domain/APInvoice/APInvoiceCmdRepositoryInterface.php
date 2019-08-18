<?php
namespace Procure\Domain\APInvoice;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface APInvoiceCmdRepositoryInterface
{

    public function store(GenericAPInvoice $trx, $generateSysNumber = false, $isPosting = false);

    public function post(GenericAPInvoice $trx, $generateSysNumber = True);

    public function storeHeader(GenericAPInvoice $trxAggregate, $generateSysNumber = false, $isPosting = false);

    public function storeRow(GenericAPInvoice $trx, APInvoiceRow $row, $isPosting = false);
    
    public function createRow($trxId, APInvoiceRow $row, $isPosting = false);
    
}

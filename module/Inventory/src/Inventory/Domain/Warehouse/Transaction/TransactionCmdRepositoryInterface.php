<?php
namespace Inventory\Domain\Warehouse\Transaction;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface TransactionCmdRepositoryInterface
{

    public function store(GenericTransaction $trx, $generateSysNumber = false, $isPosting = false);

    public function post(GenericTransaction $trx, $generateSysNumber = True);

    public function storeHeader(GenericTransaction $trxAggregate, $generateSysNumber = false, $isPosting = false);

    public function storeRow(GenericTransaction $trx, TransactionRow $row, $isPosting = false);
}

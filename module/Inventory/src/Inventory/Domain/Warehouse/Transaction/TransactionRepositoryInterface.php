<?php
namespace Inventory\Domain\Warehouse\Transaction;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface TransactionRepositoryInterface
{

    public function findAll();

    public function getById($id, $outputStragegy = null);

    public function getHeaderById($trxId, $token = null);

    public function getByUUID($uuid);

    public function store(GenericTransaction $trx, $generateSysNumber = True);

    public function post(GenericTransaction $trx, $generateSysNumber = True);

    public function storeHeader(GenericTransaction $trxAggregate);

    public function storeRow(GenericTransaction $trx, TransactionRow $row);
}

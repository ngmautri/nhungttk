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

    public function getById($id);

    public function getByUUID($uuid);

    public function store(GenericTransaction $trx, $generateSysNumber = True);

    public function storeRow(TransactionRow $row);

    public function post(GenericTransaction $trx, $generateSysNumber = True);
}
<?php
namespace Inventory\Domain\Warehouse\Transaction;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface TransactionQueryRepositoryInterface
{

    public function findAll();

    public function getById($id, $outputStragegy = null);

    public function getHeaderById($trxId, $token = null);

    public function getByUUID($uuid);
}

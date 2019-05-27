<?php
namespace Inventory\Domain\Transaction;

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
    
}

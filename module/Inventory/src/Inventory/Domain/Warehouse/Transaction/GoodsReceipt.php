<?php
namespace Inventory\Domain\Warehouse\Transaction;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GoodsReceipt extends GenericTransaction
{
    /**
     *
     * {@inheritDoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::post()
     */
    public function post(){
        
    }
   
}
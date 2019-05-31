<?php
namespace Inventory\Domain\Warehouse\Transaction\GI;

use Inventory\Domain\Warehouse\Transaction\GenericTransaction;
use Inventory\Domain\Warehouse\Transaction\GoodsReceiptInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRFromPurchasing extends GenericTransaction implements GoodsReceiptInterface
{
    
    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::specificValidation()
     */
    public function specificValidation($notification = null)
    {
       // empty
    }

  
}
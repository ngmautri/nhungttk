<?php
namespace Inventory\Domain\Warehouse\Transaction\GI;

use Inventory\Domain\Warehouse\Transaction\GenericTransaction;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIforCostCenter extends GenericTransaction
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
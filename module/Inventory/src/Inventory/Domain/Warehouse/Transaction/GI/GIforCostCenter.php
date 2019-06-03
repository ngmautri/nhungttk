<?php
namespace Inventory\Domain\Warehouse\Transaction\GI;

use Inventory\Domain\Warehouse\Transaction\GoodsIssue;
use Inventory\Domain\Warehouse\Transaction\GoodsIssueInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIforCostCenter extends GoodsIssue implements GoodsIssueInterface
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
    
    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::addTransactionRow()
     */
    public function addTransactionRow($transactionRowDTO)
    {
        
             
    }


    

  
}
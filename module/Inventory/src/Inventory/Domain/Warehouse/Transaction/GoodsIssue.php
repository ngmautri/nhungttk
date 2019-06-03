<?php
namespace Inventory\Domain\Warehouse\Transaction;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GoodsIssue extends GenericTransaction 
{
    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Domain\Warehouse\Transaction\GenericTransaction::post()
     */
    public function post(){
        //1.validate header
        
        $notification = $this->validate();
        
        //2. caculate cogs
        
         foreach($this->transactionRows as $row){
            
             /**
              * @var  \Inventory\Domain\Warehouse\Transaction\TransactionRow $row ; 
              */
             $row->getDocQuantity();
              
        }
        
        //3. do specific ation
        
        //4. store transaction
        
    }
    
    

}
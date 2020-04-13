<?php
namespace Inventory\Domain\Validator\Warehouse\Transaction;

use Inventory\Domain\Warehouse\Transaction\AbstractTransaction;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface RowValidatorInterface
{
    /**
     * 
     * @param \Inventory\Domain\Warehouse\Transaction\AbstractTransaction $rootEntity
     */
    public function validate(AbstractTransaction $rootEntity);
}


<?php
namespace Inventory\Model\GR;

use Inventory\Domain\Warehouse\Transaction\GenericWarehouseTransaction;

/**
 * Machine ID is required.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRfromPurchasing extends GenericWarehouseTransaction
{

    public function validate()
    {}
}
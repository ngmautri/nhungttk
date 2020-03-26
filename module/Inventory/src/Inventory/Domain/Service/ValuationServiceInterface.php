<?php
namespace Inventory\Domain\Service;

use Inventory\Domain\Warehouse\Transaction\GenericTransaction;
use Inventory\Domain\Warehouse\Transaction\TransactionRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface ValuationServiceInterface
{

    /**
     *
     * @param GenericTransaction $trx
     * @param TransactionRow $itemId
     * @param string $issuedQuantity
     */
    public function calculateCOGS($trx, $row);
}

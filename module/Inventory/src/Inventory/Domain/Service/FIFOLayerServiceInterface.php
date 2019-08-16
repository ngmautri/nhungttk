<?php
namespace Inventory\Domain\Service;

use Inventory\Domain\Warehouse\Transaction\GenericTransaction;
use Inventory\Domain\Warehouse\Transaction\TransactionRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface FIFOLayerServiceInterface
{

    /**
     *
     * @param GenericTransaction $trx
     * @param TransactionRow $row
     */
    public function createLayer(GenericTransaction $trx, TransactionRow $row);

    /**
     *
     * @param GenericTransaction $trx
     * @param TransactionRow $row
     */
    public function closeLayer(GenericTransaction $trx, TransactionRow $row);
}

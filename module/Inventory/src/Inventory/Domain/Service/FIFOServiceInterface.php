<?php
namespace Inventory\Domain\Service;

use Inventory\Domain\Warehouse\Transaction\GenericTransaction;
use Inventory\Domain\Warehouse\Transaction\TransactionRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface FIFOServiceInterface
{

    /**
     *
     * @param GenericTransaction $trx
     * @param TransactionRow $itemId
     * @param string $issuedQuantity
     */
    public function calculateCOGS($trx, $row);

    /**
     *
     * @param GenericTransaction $trx
     * @param TransactionRow $row
     */
    public function createLayer(GenericTransaction $trx, TransactionRow $row);

    /**
     *
     * @param GenericTransaction $trx
     */
    public function createLayersFor(GenericTransaction $trx);

    /**
     *
     * @param GenericTransaction $trx
     * @param TransactionRow $row
     */
    public function closeLayers(GenericTransaction $trx, TransactionRow $row);

    /**
     *
     * @param GenericTransaction $trx
     */
    public function closeLayersOf(GenericTransaction $trx);
}

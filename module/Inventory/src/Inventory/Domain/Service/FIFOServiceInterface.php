<?php
namespace Inventory\Domain\Service;

use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface FIFOServiceInterface
{

    /**
     *
     * @param GenericTrx $trx
     * @param TrxRow $row
     */
    public function calculateCOGS(GenericTrx $trx, TrxRow $row);

    /**
     *
     * @param GenericTrx $trx
     * @param TrxRow $row
     */
    public function createLayer(GenericTrx $trx, TrxRow $row);

    /**
     *
     * @param GenericTrx $trx
     */
    public function createLayersFor(GenericTrx $trx);

    /**
     *
     * @param GenericTrx $trx
     * @param TrxRow $row
     */
    public function closeLayers(GenericTrx $trx, TrxRow $row);

    /**
     *
     * @param GenericTrx $trx
     */
    public function closeLayersOf(GenericTrx $trx);
}

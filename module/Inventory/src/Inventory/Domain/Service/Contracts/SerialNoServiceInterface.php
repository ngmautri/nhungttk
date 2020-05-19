<?php
namespace Inventory\Domain\Service\Contracts;

use Procure\Domain\GoodsReceipt\GRSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface SerialNoServiceInterface
{

    /**
     *
     * @param GRSnapshot $trx
     */
    public function createSerialNoFor(GRSnapshot $trx);

    /**
     *
     * @param GRSnapshot $trx
     */
    public function reverseSerialNoFor(GRSnapshot $trx);
}

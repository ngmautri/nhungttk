<?php
namespace Inventory\Domain\Service;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface FIFOLayerServiceInterface
{

    /**
     *
     * @param int $transactionId
     * @param int $itemId
     * @param string $issuedQuantity
     */
    public function calculateCOGS($transactionId, $itemId, $issuedQuantity);
}

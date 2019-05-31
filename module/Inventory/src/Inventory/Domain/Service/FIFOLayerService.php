<?php
namespace Inventory\Domain\Service;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface FIFOLayerServiceInterface
{

    public function calculateCOGS($itemId, $qty, \DateTime $transactionDate);
}

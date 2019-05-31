<?php
namespace Inventory\Service;

use Application\Service\AbstractService;
use Inventory\Domain\Service\FIFOLayerServiceInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FIFOLayerService extends AbstractService implements FIFOLayerServiceInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\FIFOLayerServiceInterface::calculateCOGS()
     */
    public function calculateCOGS($itemId, $qty, \DateTime $transactionDate)
    {}
}

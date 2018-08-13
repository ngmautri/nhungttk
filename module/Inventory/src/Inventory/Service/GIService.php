<?php
namespace Inventory\Service;

use Inventory\Model\GI\GIStrategyFactory;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIService extends AbstractInventoryService
{

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $target
     * @param \Application\Entity\NmtInventoryTrx $trx
     * @param \Application\Entity\MlaUsers $u
     */
    public function addRow($target, $trx, $u)
    {
        if (! $target instanceof \Application\Entity\NmtInventoryMv) {
            throw new \Exception("Invalid Argument! Inventory Moverment Object can't not be found.");
        }

        if (! $trx instanceof \Application\Entity\NmtInventoryTrx) {
            throw new \Exception("Invalid Argument!Object can't not be found.");
        }

        // GI Strategy.
        $giStrategy = GIStrategyFactory::getGIStrategy($target->getMovementType());

        if (! $giStrategy instanceof \Inventory\Model\GI\AbstractGIStrategy) {
            throw new \Exception("Invalid Argument! No strategy found.");
        }

        $giStrategy->check($trx, $trx->getItem(), $u);

        $this->getDoctrineEM()->persist($trx);
    }

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $entity
     * @param \Application\Entity\MlaUsers $u,
     * @param bool $isFlush,
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function post($entity, $u, $isFlush = false)
    {}
}

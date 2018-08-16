<?php
namespace Inventory\Service;

use Inventory\Model\GI\GIStrategyFactory;
use Inventory\Model\GI\AbstractGIStrategy;
use Zend\Math\Rand;
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
        
        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }
        

        // GI Strategy.
        $giStrategy = GIStrategyFactory::getGIStrategy($target->getMovementType());

        if (! $giStrategy instanceof \Inventory\Model\GI\AbstractGIStrategy) {
            throw new \Exception("Invalid Argument! No strategy found.");
        }

        // check on-hand quantity.
        
        $giStrategy->check($trx, $trx->getItem(), $u);
        
        $trx->setTrxDate($target->getMovementDate());
        $trx->setDocCurrency($target->getCurrency());
        $trx->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
        $trx->setCreatedBy($u);
        $trx->setCreatedOn(new \DateTime());
        
        $this->getDoctrineEM()->persist($trx);
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param \Application\Entity\MlaUsers $u,
     * @param bool $isFlush,
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function post($entity, $u, $isFlush = false)
    {
        if (! $entity instanceof \Application\Entity\NmtInventoryMv) {
            throw new \Exception("Invalid Argument! Inventory Moverment Object can't not be found.");
        }
        
        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("User can't not be identified for this transaction");
        }
        
        $postingStrategy = GIStrategyFactory::getGIStrategy($entity->getMovementType());
        
        if(!$postingStrategy instanceof AbstractGIStrategy){
            throw new \Exception("Posting Strategy can't not be identified for this inventory movement type!");
        }
        
        // do posting now
        $postingStrategy->setContextService($this);
        $postingStrategy->doPosting($entity, $u);
        
    }
}

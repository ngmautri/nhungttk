<?php
namespace Inventory\Model\GI;

use Zend\Math\Rand;

/**
 * Machine ID is required.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIforRepairMachine extends AbstractGIStrategy
{

    /**
     *
     * @param \Application\Entity\NmtInventoryTrx $trx
     * @param \Application\Entity\NmtInventoryItem $item
     * @param \Application\Entity\MlaUsers $u
     *
     * {@inheritdoc}
     * @see \Inventory\Model\GI\AbstractGIStrategy::check()
     */
    public function check($trx, $item, $u)
    {
        if (! $trx instanceof \Application\Entity\NmtInventoryTrx) {
            throw new \Exception("Invalid Argument! Inventory Moverment can't not be found.");
        }

        if (! $item instanceof \Application\Entity\NmtInventoryItem) {
            throw new \Exception("Invalid Argument! Item can't not be found.");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        // OK to post
        // +++++++++++++++++++

        // Required machine
        if ($trx->getIssueFor() == null) {
            throw new \Exception("Invalid Argument! Machine is not give.");
        }

        if ($trx->getIssueFor() === $item) {
            throw new \Exception("Invalid Argument! transaction is not posible!");
        }

        $trx->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));

        $trx->setCreatedBy($u);
        $trx->setCreatedOn(new \DateTime());
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param \Application\Entity\MlaUsers $u
     * {@inheritdoc}
     * @see \Inventory\Model\GI\AbstractGIStrategy::doPosting()
     */
    public function doPosting($entity, $u)
    {}
    
    /**
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param \Application\Entity\MlaUsers $u
     * @param \DateTime $reversalDate
 
     * {@inheritDoc}
     * @see \Inventory\Model\GI\AbstractGIStrategy::reverse()
     */
    public function reverse($entity, $u, $reversalDate)
    {}

}
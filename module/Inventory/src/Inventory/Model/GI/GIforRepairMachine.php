<?php
namespace Inventory\Model\GI;

use Zend\Math\Rand;
use Inventory\Service\FIFOLayerService;

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
    {

        $criteria = array(
             'movement' => $entity,
        );
        
        $sort = array(
        );
        
        $rows = $this->contextService->getDoctrineEM()->getRepository('Application\Entity\NmtInventoryTrx')->findBy($criteria, $sort);
        
        
        if (count($rows) == 0) {
            throw new \Exception("Movement is empty");
        }
        
        $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
        $entity->setIsDraft(0);
        $this->contextService->getDoctrineEM()->persist($entity);
        
        $fifoLayerService = new FIFOLayerService();
        $fifoLayerService->setDoctrineEM($this->contextService->getDoctrineEM());

        foreach ($rows as $r) {
            /** @var \Application\Entity\NmtInventoryTrx $r */
        
            $r->setDocStatus($entity->getDocStatus());
            $this->contextService->getDoctrineEM()->persist($r);
            
            // update FIFO Layer
            $fifoLayerService->valuateTrx($r, $r->getItem(), $r->getQuantity(), $u);
        
             // Take defect part back to stock.
            $item_ex = new \Application\Entity\NmtInventoryItemExchange();
            $item_ex->setItem($r->getItem());
            $item_ex->setMovementType($entity->getMovementType());
            $item_ex->setFlow(\Inventory\Model\Constants::WH_TRANSACTION_IN);
            $item_ex->setQuantity($r->getQuantity());
            $item_ex->setCreatedBy($u);
            $item_ex->setCreatedOn($r->getTrxDate());
            $item_ex->setWh($r->getWh());
            $item_ex->setTrx($r);
            $this->contextService->getDoctrineEM()->persist($item_ex);
            
            
            // generate Juanal voucher.
             
        }
        
        $this->contextService->getDoctrineEM()->flush();
    }

    /**
     *
     * @param \Application\Entity\NmtInventoryMv $entity
     * @param \Application\Entity\MlaUsers $u
     * @param \DateTime $reversalDate
     *
     * {@inheritdoc}
     * @see \Inventory\Model\GI\AbstractGIStrategy::reverse()
     */
    public function reverse($entity, $u, $reversalDate)
    {}
}
<?php
namespace Inventory\Model\GR;

use Zend\Math\Rand;
use Zend\Validator\Date;
use Inventory;

/**
 * Machine ID is required.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRfromPurchasingReversal extends Inventory\Model\AbstractTransactionStrategy
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\InventoryTransactionInterface::getFlow()
     */
    public function getFlow()
    {
        return \Inventory\Model\Constants::WH_TRANSACTION_OUT;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\InventoryTransactionInterface::getTransactionIdentifer()
     */
    public function getTransactionIdentifer()
    {
        return \Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING_REVERSAL;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::check()
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
            throw new \Exception("Invalid Argument! It is not posible to use the same item. Please select other!");
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::doPosting()
     */
    public function doPosting($entity, $u, $isFlush = false)
    {
        $criteria = array(
            'movement' => $entity
        );

        $sort = array();

        $rows = $this->contextService->getDoctrineEM()
            ->getRepository('Application\Entity\NmtInventoryTrx')
            ->findBy($criteria, $sort);

        if (count($rows) == 0) {
            throw new \Exception("Movement is empty");
        }

        $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
        $entity->setIsDraft(0);
        $entity->setIsPosted(1);

        $this->contextService->getDoctrineEM()->persist($entity);
        $this->contextService->getDoctrineEM()->flush();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::reverse()
     */
    public function reverse($entity, $u, $reversalDate, $isFlush = false)
    {}

    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::createMovement()
     */
    public function createMovement($rows, $u, $isFlush = false, $movementDate = null, $wareHouse = null, $trigger=null)
    {
        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (count($rows) == 0) {
            throw new \Exception("Invalid Argument! Nothing to create.");
        }

        if ($movementDate != null) {
            if (! $movementDate instanceof \DateTime) {
                throw new \Exception('Movement Date is not correct or empty!');
            }
        }
        if ($wareHouse != null) {
            if (! $wareHouse instanceof \Application\Entity\NmtInventoryWarehouse) {
                throw new \Exception('Warehouse is not correct or empty!');
            }
        }

        $createdOn = new \DateTime();

        $mv = new \Application\Entity\NmtInventoryMv();

        
        $mv->setMovementFlow(\Inventory\Model\Constants::WH_TRANSACTION_OUT);
        $mv->setMovementType(\Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING_REVERSAL);
        $mv->setTrxType(\Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING_REVERSAL);
        $mv->setDocStatus(\Application\Model\Constants::DOC_STATUS_REVERSED);

        $mv->setIsPosted(1);
        $mv->setIsDraft(0);
        $mv->setIsActive(1);
        $mv->setIsReversed(1);
        
        $mv->setRemarks('[Reversal]');
        $this->contextService->getDoctrineEM()->persist($mv);

        $n = 0;

        /**
         *
         * @todo: Reversal Good Receipt WH
         */
        foreach ($rows as $stock_gr_entity) {

            /** @var \Application\Entity\NmtInventoryTrx $stock_gr_entity */

            if (! $stock_gr_entity instanceof \Application\Entity\NmtInventoryTrx) {
                continue;
            }

            $n ++;

            $stock_gr_entity->setIsReversed(1);
            $stock_gr_entity->setReversalDate($movementDate);
            // $stock_gr_entity->setReversalReason($r->getReversalReason());
            // $stock_gr_entity->setLastchangedBy($u);
            // $stock_gr_entity->setLastchangeOn($createdOn);
            $this->contextService->getDoctrineEM()->persist($stock_gr_entity);

            // reversal
            /** @var \Application\Entity\NmtInventoryTrx $stock_gr_entity_new ; */
            $stock_gr_entity_new = clone ($stock_gr_entity);

            $stock_gr_entity->setDocType(\Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING_REVERSAL);
            $stock_gr_entity_new->setMovement($mv);
            $stock_gr_entity_new->setDocStatus($mv->getDocStatus());

            $stock_gr_entity_new->setFlow(\Application\Model\Constants::WH_TRANSACTION_OUT);
            $stock_gr_entity_new->setCreatedBy($u);
            $stock_gr_entity_new->setCreatedOn($createdOn);
            $stock_gr_entity_new->setToken(\Zend\Math\Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . \Zend\Math\Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));

            $this->contextService->getDoctrineEM()->persist($stock_gr_entity_new);

            $criteria = array(
                'sourceClass' => get_class($stock_gr_entity),
                'sourceId' => $stock_gr_entity->getId()
            );

            /**
             *
             * @todo: Reversal FIFO Layer
             * @var \Application\Entity\NmtInventoryFifoLayer $fifoLayer ;
             */
            $fifoLayer = $this->contextService->getDoctrineEM()
                ->getRepository('Application\Entity\NmtInventoryFifoLayer')
                ->findOneBy($criteria);

            if ($fifoLayer !== null) {
                $fifoLayer->setIsClosed(1);
                $fifoLayer->setIsReversed(1);
                $fifoLayer->setReversalDate($movementDate);
            }

        /**
         *
         * @todo: Reversal FIFO Layer Consumption.
         * @var \Application\Entity\NmtInventoryFifoLayerConsume $fifoLayer_consum ;
         */
        }

        if ($n > 0) {
            $mv->setMovementDate($movementDate);
            $mv->setWarehouse($wareHouse);
            $mv->setCreatedBy($u);
            $mv->setCreatedOn($createdOn);
            $mv->setToken(\Zend\Math\Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . \Zend\Math\Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            $mv->setSysNumber($this->contextService->getControllerPlugin()
                ->getDocNumber($mv));
            $this->contextService->getDoctrineEM()->persist($mv);
        }
        
        if ($isFlush == true) {
            $this->contextService->getDoctrineEM()->flush();
        }

        if ($trigger==null){
            $trigger = __METHOD__;
        }
        $m = sprintf('[OK] Warehouse Goods Receipt %s reversed', $mv->getSysNumber());
        $this->contextService->getEventManager()->trigger('inventory.activity.log', $trigger, array(
            'priority' => \Zend\Log\Logger::INFO,
            'message' => $m,
            'createdBy' => $u,
            'createdOn' => $createdOn,
            'isFlush' => $isFlush,
        ));
    }
}
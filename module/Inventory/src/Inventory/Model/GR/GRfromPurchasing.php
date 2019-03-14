<?php
namespace Inventory\Model\GR;

use Zend\Math\Rand;
use Zend\Validator\Date;
use Inventory\Model\AbstractTransactionStrategy;

/**
 * Machine ID is required.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRfromPurchasing extends AbstractTransactionStrategy
{
    
    /**
     *
     * {@inheritDoc}
     * @see \Inventory\Model\InventoryTransactionInterface::getFlow()
     */
    public function getFlow()
    {
        return \Application\Model\Constants::WH_TRANSACTION_IN;
        
    }
    
    /**
     * 
     * {@inheritDoc}
     * @see \Inventory\Model\InventoryTransactionInterface::getTransactionIdentifer()
     */
    public function getTransactionIdentifer()
    {
        return \Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING;
    }
  
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

   
    public function reverse($entity, $u, $reversalDate, $isFlush = false)
    {}

   
    public function createMovement($rows, $u, $isFlush = false, $movementDate=null, $wareHouse = null)
    {
        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("Invalid Argument! User can't be indentided for this transaction.");
        }

        if (count($rows) == 0) {
            throw new \Exception("Invalid Argument! Nothing to create.");
        }
        
        if($movementDate!=null){
            if (!$movementDate instanceof \DateTime) {
                throw new \Exception('Movement Date is not correct or empty!');
            }
        }
        if($wareHouse!=null){
            if (!$wareHouse instanceof \Application\Entity\NmtInventoryWarehouse) {
                throw new \Exception('Warehouse is not correct or empty!');
            }
       }
        
       
       
       
        $createdOn = new \DateTime();

        $mv = new \Application\Entity\NmtInventoryMv();
        $mv->setMovementFlow(\Inventory\Model\Constants::WH_TRANSACTION_IN);
        $mv->setMovementType(\Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING);
        $mv->setDocStatus(\Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING);
        
        $mv->setIsPosted(1);
        $mv->setIsDraft(0);
        $mv->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
        $this->contextService->getDoctrineEM()->persist($mv);

        $n = 0;
        foreach ($rows as $r) {

            /** @var \Application\Entity\NmtInventoryTrx $r */
            if (! $r instanceof \Application\Entity\NmtInventoryTrx) {
                continue;
            }

            $n ++;
            $r->setMovement($mv);
            $r->setDocStatus($mv->getDocStatus());
            $r->setDocType(\Inventory\Model\Constants::INVENTORY_GR_FROM_PURCHASING);
            
            $this->contextService->getDoctrineEM()->persist($r);

            // created FIFO is needed
            if ($r->getItem() != null) {
              
                if ($r->getItem()->getIsStocked() == 1) {
                    
                    
                    /**
                     *
                     * @todo: Create FIFO Layer
                     * @todo: recalculate price for inventory unit.
                     */
                    $fifoLayer = new \Application\Entity\NmtInventoryFifoLayer();

                    $fifoLayer->setIsClosed(0);
                    $fifoLayer->setItem($r->getItem());
                    $fifoLayer->setQuantity($r->getQuantity());
                    
                    // set WH
                    $fifoLayer->setWarehouse($r->getWh());                    

                    // will be changed uppon inventory transaction.
                    $fifoLayer->setOnhandQuantity($r->getQuantity());
                    $fifoLayer->setDocUnitPrice($r->getVendorUnitPrice());
                    $fifoLayer->setLocalCurrency($r->getCurrency());
                    $fifoLayer->setExchangeRate($r->getExchangeRate());
                    $fifoLayer->setPostingDate($r->getTrxDate());
                    $fifoLayer->setSourceClass(get_class($r));
                    $fifoLayer->setSourceId($r->getID());
                    $fifoLayer->setSourceToken($r->getToken());

                    $fifoLayer->setToken(Rand::getString(22, \Application\Model\Constants::CHAR_LIST, true));
                    $fifoLayer->setCreatedBy($u);
                    $fifoLayer->setCreatedOn($r->getCreatedOn());
                    $this->contextService->getDoctrineEM()->persist($fifoLayer);

                /**
                 *
                 * @todo: Calculate Moving Average Price.
                 */
                }
            }
        }

        if ($n >0 ) {
            $mv->setMovementDate($movementDate);
            $mv->setWarehouse($wareHouse);
            $mv->setCreatedBy($u);
            $mv->setCreatedOn($createdOn);
            $mv->setToken(\Zend\Math\Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . \Zend\Math\Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));
            $mv->setSysNumber($this->contextService->getControllerPlugin()
                ->getDocNumber($mv));
            $this->contextService->getDoctrineEM()->persist($mv);

            if ($isFlush == true) {
                $this->contextService->getDoctrineEM()->flush();
            }

            $m = sprintf('[OK] Warehouse Goods Receipt %s posted', $mv->getSysNumber());
            $this->contextService->getEventManager()->trigger('inventory.activity.log', __METHOD__, array(
                'priority' => \Zend\Log\Logger::INFO,
                'message' => $m,
                'createdBy' => $u,
                'createdOn' => $createdOn
            ));
        }
    }
    public function getFlow()
    {}

   

}
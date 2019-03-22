<?php
namespace Inventory\Model\GI;
use Zend\Math\Rand;

/**
 * Machine ID is required, exchange part.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIforRepairMachine extends \Inventory\Model\AbstractTransactionStrategy
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::validateRow()
     */
    public function validateRow($entity, $data, $u, $isNew, $isPosting)
    {
        $errors = array();

        // addtional information.
        if (isset($data['issue_for_id'])) {
            $issue_for_id = $data['issue_for_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "$issue_for_id"');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 2 ====== //
        /**@var \Application\Entity\NmtInventoryItem $issue_for_item ;*/
        $issue_for_item = $this->getContextService()
            ->getDoctrineEM()
            ->getRepository('Application\Entity\NmtInventoryItem')
            ->find($issue_for_id);

        $no_errors = 0;
        if ($issue_for_item == null) {
            $errors[] = $this->getContextService()
                ->getControllerPlugin()
                ->translate('Machine ID is required');
        } else {
            if ($issue_for_item == $entity->getItem()) {
                $errors[] = $this->getContextService()
                    ->getControllerPlugin()
                    ->translate('Item is the same');
                $no_errors ++;
            }

            if ($issue_for_item->getIsFixedAsset() == 0) {
                $errors[] = $this->getContextService()
                    ->getControllerPlugin()
                    ->translate('Item is not a machine');
                $no_errors ++;
            }

            if ($no_errors == 0) {
                $entity->setIssueFor($issue_for_item);
            }
        }
        return $errors;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\InventoryTransactionInterface::getFlow()
     */
    public function getFlow()
    {
        return \Application\Model\Constants::WH_TRANSACTION_OUT;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\InventoryTransactionInterface::getTransactionIdentifer()
     */
    public function getTransactionIdentifer()
    {
        return \Inventory\Model\Constants::INVENTORY_GI_FOR_REPAIR_MACHINE;
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
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::doPosting()
     */
    public function doPosting($entity, $u, $isFlush = false)
    {
        // check warehouse 
        $wh =  $entity->getWarehouse();
         
        $criteria = array(
            'warehouse' => $wh,
            'isReturnLocation'=> 1,
        );
        
        /** @var \Application\Entity\NmtInventoryWarehouseLocation $returnLocation */
        $returnLocation = $this->contextService->getDoctrineEM()
        ->getRepository('Application\Entity\NmtInventoryWarehouseLocation')
        ->findOneBy($criteria);
        
        if($returnLocation==null){
            throw new \Exception('Return Location is not defined. Please check warehouse set-up');
        }
        
        /** @var \Application\Entity\NmtInventoryMv $newEntity */
        $newEntity = clone($entity);
        
        
        $newEntity->setMovementType($entity->getMovementType()."+1"); // IMPORTANT
        
        $newEntity->setMovementFlow(\Inventory\Model\Constants::WH_TRANSACTION_IN); // IMPORTANT        
        $newEntity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true));
        $newEntity->setRemarks('Automatic posting');
        $newEntity->setSysNumber($this->contextService->getControllerPlugin()
            ->getDocNumber($newEntity));
        $newEntity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
        $newEntity->setIsDraft(0);
        $newEntity->setIsPosted(1);
        
        $this->contextService->getDoctrineEM()->persist($newEntity);
        
        
        $criteria = array(
            'movement' => $entity
        );

        $sort = array();

        $rows = $this->contextService->getDoctrineEM()
            ->getRepository('Application\Entity\NmtInventoryTrx')
            ->findBy($criteria, $sort);

            $n=0;
        foreach ($rows as $r) {
            /** @var \Application\Entity\NmtInventoryTrx $r */

            if ($r->getQuantity() == 0) {
                continue;
            }
            $n++;
            /** @var \Application\Entity\NmtInventoryTrx $newRow */
            $newRow =  clone($r);
            
            $newRow->setDocStatus($newEntity->getDocStatus());
            
            $newRow->setMovement($newEntity);
            $newRow->setIssueFor(null);
            $newRow->setCogsDoc(0);
            $newRow->setCogsLocal(0);            
            $newRow->setFlow(\Inventory\Model\Constants::WH_TRANSACTION_IN); // IMPORTANT
            $newRow->setWhLocation($returnLocation); // IMPORTANT
            $newRow->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true));
            $newRow->setRemarks('Automatic posting');
            $newRow->setSysNumber($newEntity->getSysNumber()."-".$n);
            $this->contextService->getDoctrineEM()->persist($newRow);
        }
         
        $this->contextService->getDoctrineEM()->flush();
    }

   /**
    * 
    * {@inheritDoc}
    * @see \Inventory\Model\AbstractTransactionStrategy::reverse()
    */
    public function reverse($entity, $u, $reversalDate, $reversalReason, $isFlush = false)
    {
        // check warehouse
        $wh =  $entity->getWarehouse();
        
        $criteria = array(
            'warehouse' => $wh,
            'isReturnLocation'=> 1,
        );
        
        /** @var \Application\Entity\NmtInventoryWarehouseLocation $returnLocation */
        $returnLocation = $this->contextService->getDoctrineEM()
        ->getRepository('Application\Entity\NmtInventoryWarehouseLocation')
        ->findOneBy($criteria);
        
        if($returnLocation==null){
            throw new \Exception('Return Location is not defined. Please check warehouse set-up');
        }
        
        /** @var \Application\Entity\NmtInventoryMv $newEntity */
        $newEntity = clone($entity);
        
        $newEntity->setIsReversed(1);
        $newEntity->setReversalReason($reversalReason);
        $newEntity->setReversalDate(new \DateTime($reversalDate));
         
        $newEntity->setMovementFlow(\Inventory\Model\Constants::WH_TRANSACTION_OUT); // IMPORTANT
        $newEntity->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true));
        $newEntity->setRemarks('[Reversal] Automatic posting');
        $newEntity->setSysNumber($this->contextService->getControllerPlugin()
            ->getDocNumber($newEntity));
        $entity->setDocStatus(\Application\Model\Constants::DOC_STATUS_REVERSED);
        $entity->setIsDraft(0);
        $entity->setIsPosted(1);
        
        $this->contextService->getDoctrineEM()->persist($newEntity);
        
        
        $criteria = array(
            'movement' => $entity
        );
        
        $sort = array();
        
        $rows = $this->contextService->getDoctrineEM()
        ->getRepository('Application\Entity\NmtInventoryTrx')
        ->findBy($criteria, $sort);
        
        $n=0;
        foreach ($rows as $r) {
            /** @var \Application\Entity\NmtInventoryTrx $r */
            
            if ($r->getQuantity() == 0) {
                continue;
            }
            $n++;
            /** @var \Application\Entity\NmtInventoryTrx $newRow */
            $newRow =  clone($r);
            
            $newRow->setDocStatus($newEntity->getDocStatus());
            $newRow->setMovement($newEntity);
            $newRow->setIssueFor(null);
            $newRow->setCogsDoc(0);
            $newRow->setCogsLocal(0);
            $newRow->setFlow($newEntity->getMovementFlow()); // IMPORTANT
            $newRow->setWhLocation($returnLocation); // IMPORTANT
            
            $newRow->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true));
            $newRow->setRemarks('[Reversal] Automatic posting');
            $newRow->setSysNumber($newEntity->getSysNumber()."-".$n);
            $this->contextService->getDoctrineEM()->persist($newRow);
        }
        
        $this->contextService->getDoctrineEM()->flush();
        
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::createMovement()
     */
    public function createMovement($rows, $u, $isFlush = false, $movementDate = null, $wareHouse = null, $trigger = null)
    {}
}
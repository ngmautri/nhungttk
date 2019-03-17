<?php
namespace Inventory\Model\GI;

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
        $criteria = array(
            'movement' => $entity
        );

        $sort = array();

        $rows = $this->contextService->getDoctrineEM()
            ->getRepository('Application\Entity\NmtInventoryTrx')
            ->findBy($criteria, $sort);

        foreach ($rows as $r) {
            /** @var \Application\Entity\NmtInventoryTrx $r */

            if ($r->getQuantity() == 0) {
                continue;
            }

            // Exchanging Part.
            $item_ex = new \Application\Entity\NmtInventoryItemExchange();
            $item_ex->setItem($r->getItem());
            $item_ex->setMovementType($entity->getMovementType());
            $item_ex->setFlow(\Inventory\Model\Constants::WH_TRANSACTION_IN);
            $item_ex->setQuantity($r->getQuantity());
            $item_ex->setCreatedBy($u);
            $item_ex->setCreatedOn($r->getTrxDate());
            $item_ex->setWh($r->getWh());
            $item_ex->setTrx($r);
            $item_ex->setRemarks("Auto receipt old/defect part back into store!");
            $this->contextService->getDoctrineEM()->persist($item_ex);
        }
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
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::createMovement()
     */
    public function createMovement($rows, $u, $isFlush = false, $movementDate = null, $wareHouse = null, $trigger = null)
    {}
}
<?php
namespace Inventory\Model\GI;

use Zend\Math\Rand;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GIforTransferToLocation extends \Inventory\Model\AbstractTransactionStrategy
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::validateHeader()
     */
    public function validateHeader($entity, $data, $u, $isNew, $isPosting)
    {
        $errors = array();

        if (isset($data['source_wh_id'])) {
            $source_wh_id = $data['source_wh_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "source_wh_id"');
        }

        if (isset($data['source_location_id'])) {
            $source_location_id = $data['source_location_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "source_location_id"');
        }

        if (isset($data['target_location_id'])) {
            $target_location_id = $data['target_location_id'];
        } else {
            $errors[] = $this->controllerPlugin->translate('No input given "target_location_id"');
        }

        if (count($errors) > 0) {
            return $errors;
        }

        // ====== VALIDATED 2 ====== //

        // yes it is transfer transaction
        $entity->setIsTransferTransaction(1);

        /**@var \Application\Entity\NmtInventoryWarehouse $source_wh ;*/
        $source_wh = $this->getContextService()
            ->getDoctrineEM()
            ->getRepository('Application\Entity\NmtInventoryWarehouse')
            ->find($source_wh_id);

        $n_check = 0;

        // check source warehouse
        if ($source_wh == null) {
            $errors[] = $this->getContextService()
                ->getControllerPlugin()
                ->translate('Source Warehouse is required');
        } else {
            $n_check ++;
            $entity->setWarehouse($source_wh);
        }

        // check source location
        $criteria = array(
            "warehouse" => $source_wh,
            "id" => $source_location_id
        );

        /**@var \Application\Entity\NmtInventoryWarehouseLocation $source_location ;*/
        $source_location = $this->getContextService()
            ->getDoctrineEM()
            ->getRepository('Application\Entity\NmtInventoryWarehouseLocaction')
            ->findOneBy($criteria);

        if ($source_location == null) {
            $errors[] = $this->getContextService()
                ->getControllerPlugin()
                ->translate('Source Location is required');
        }

        // check target location
        $criteria = array(
            "warehouse" => $source_wh,
            "id" => $source_location_id
        );

        /**@var \Application\Entity\NmtInventoryWarehouseLocation $target_location ;*/
        $target_location = $this->getContextService()
            ->getDoctrineEM()
            ->getRepository('Application\Entity\NmtInventoryWarehouseLocaction')
            ->findOneBy($criteria);

        if ($target_location == null) {
            $errors[] = $this->getContextService()
                ->getControllerPlugin()
                ->translate('Target Location is required');
        }
        
        // Check availability in source warehouse.
        

        return $errors;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::validateRow()
     */
    public function validateRow($entity, $data, $u, $isNew, $isPosting)
    {
        // no need to check.
    }

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
        return \Inventory\Model\Constants::INVENTORY_GI_FOR_TRANFER_WAREHOUSE;
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
            throw new \Exception("WH Transaction is empty");
        }

        // ====== VALIDATED 1 ====== //

        /** @var \Application\Entity\NmtInventoryMv $newEntity ;*/
        $newEntity = clone ($entity);

        // create new header for new warehouse.
        $newEntity->setMovementFlow(\Inventory\Model\Constants::WH_TRANSACTION_IN);
        $newEntity->setWarehouse($entity->getTargetWarehouse());

        $newEntity->setDocStatus(\Application\Model\Constants::DOC_STATUS_POSTED);
        $newEntity->setDocType(\Inventory\Model\Constants::INVENTORY_GR_FROM_TRANSFER_WAREHOUSE);
        $newEntity->setMovementType(\Inventory\Model\Constants::INVENTORY_GR_FROM_TRANSFER_WAREHOUSE);

        $newEntity->setToken(Rand::getString(15, \Application\Model\Constants::CHAR_LIST, true));
        $newEntity->setSysNumber($this->contextService->getControllerPlugin()
            ->getDocNumber($newEntity));
        $newEntity->setRemarks('Goods transfer - automatically generated');

        $this->contextService->getDoctrineEM()->persist($newEntity);

        $n = 0;
        foreach ($rows as $r) {

            /** @var \Application\Entity\NmtInventoryTrx $r */
            if ($r->getQuantity() == 0) {
                continue;
            }

            /**
             *
             * @todo: Get fifo layer consumption, and create new line with target warehouse.
             */
            $criteria = array(
                'inventoryTrx' => $r
            );

            $consums = $this->contextService->getDoctrineEM()
                ->getRepository('Application\Entity\NmtInventoryFifoLayerConsume')
                ->findBy($criteria);

            if (count($consums) > 0) {

                /** @var \Application\Entity\NmtInventoryFifoLayerConsume $c */

                foreach ($consums as $c) {

                    // create new row
                    /** @var \Application\Entity\NmtInventoryTrx $newRow */
                    $newRow = new \Application\Entity\NmtInventoryTrx();

                    // important!
                    $newRow->setIsActive(1);
                    $newRow->setTrxDate($r->getTrxDate());

                    $newRow->setMovement($newEntity);
                    $newRow->setWh($newEntity->getTargetWarehouse());

                    $newRow->setDocType($newEntity->getMovementType());
                    $newRow->setRemarks('Goods transfer - automatically generated');
                    $newRow->setTransactionType($newEntity->getMovementType());

                    // set opposite flow.
                    $newRow->setItem($r->getItem());
                    $newRow->setFlow($newEntity->getMovementFlow());
                    $newRow->setQuantity($c->getQuantity());
                    $newRow->setDocQuantity($c->getQuantity());
                    $newRow->setLocalCurrency($c->getLocalCurrency());
                    $newRow->setExchangeRate($c->getExchangeRate());

                    $newRow->setVendorUnitPrice($c->getDocUnitPrice());
                    $newRow->setDocUnitPrice($c->getDocUnitPrice());

                    $newRow->setToken(Rand::getString(15, \Application\Model\Constants::CHAR_LIST, true));
                    $newRow->setCreatedBy($u);
                    $newRow->setCreatedOn($r->getCreatedOn());

                    $this->contextService->getDoctrineEM()->persist($newRow);

                    // create FIFO layer for this line
                    $this->createFIFOLayerByLine($newRow, $u);
                    $n ++;
                }
            }
        }

        // throw new \Exception('Something wrong stopped' . count($consums));

        if ($n == 0) {
            throw new \Exception('Something wrong. Good receipt for new warehouse can not be created ' . count($consums));
        }

        // Need to flush the the transaction.
        $this->contextService->getDoctrineEM()->flush();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::createMovement()
     */
    public function createMovement($rows, $u, $isFlush = false, $movementDate = null, $wareHouse = null, $trigger = null)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::check()
     */
    public function check($trx, $item, $u)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Model\AbstractTransactionStrategy::reverse()
     */
    public function reverse($entity, $u, $reversalDate, $reversalReason, $isFlush = false)
    {}
}
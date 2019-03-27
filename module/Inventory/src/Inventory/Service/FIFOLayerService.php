<?php
namespace Inventory\Service;

use Application\Service\AbstractService;
use Zend\Math\Rand;
use Doctrine\ORM\Query\ResultSetMappingBuilder;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FIFOLayerService extends AbstractService
{

    /**
     *
     * @param \Application\Entity\NmtInventoryTrx $trx
     * @param \Application\Entity\NmtInventoryItem $item
     * @param \Application\Entity\NmtInventoryWarehouse $warehouse
     * @param double $issuedQuantity
     * @param \Application\Entity\MlaUsers $u
     * @throws \Exception
     * @return number
     */
    public function calculateCOGS($trx, $item, $warehouse, $issuedQuantity, $u, $isFlush = false)
    {
        if ($trx == null) {
            throw new \Exception("Invalid Argurment!");
        }

        if ($item == null) {
            throw new \Exception("Invalid Argurment! Item not found.");
        }

        if ($warehouse == null) {
            throw new \Exception("Invalid Argurment! warehouse not found.");
        }

        if ($issuedQuantity == 0) {
            throw new \Exception("Nothing to valuate!");
        }

        $sql = "SELECT * FROM nmt_inventory_fifo_layer WHERE 1 %s ORDER BY nmt_inventory_fifo_layer.posting_date ASC";

        $sql1 = sprintf("AND nmt_inventory_fifo_layer.posting_date <='%s'
AND nmt_inventory_fifo_layer.is_closed=0 
AND nmt_inventory_fifo_layer.item_id=%s 
AND nmt_inventory_fifo_layer.warehouse_id=%s", $trx->getTrxDate()->format('Y-m-d H:i:s'), $item->getId(), $warehouse->getId());

        $sql = sprintf($sql, $sql1);

        $rsm = new ResultSetMappingBuilder($this->doctrineEM);
        $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryFIFOLayer', 'nmt_inventory_fifo_layer');
        $query = $this->doctrineEM->createNativeQuery($sql, $rsm);
        $layers = $query->getResult();

        if (count($layers) == 0) {
            $m = $this->controllerPlugin->translate("Goods Issue imposible. Please check the stock quantity and the issue date");
            throw new \Exception($m);
        }

        $cogs = 0;

        $total_onhand = 0;
        $totalIssueQty = $issuedQuantity;

        /**
         *
         * @todo Get Layer and caculate Consumption.
         */
        foreach ($layers as $layer) {
            /**@var \Application\Entity\NmtInventoryFIFOLayer $layer ;*/

            $on_hand = $layer->getOnhandQuantity();
            $total_onhand += $on_hand;

            if ($issuedQuantity == 0) {
                break;
            }

            $consumpted_qty = 0;

            if ($on_hand <= $issuedQuantity) {

                // create comsuption of all, close this layer
                $consumpted_qty = $on_hand;

                $layer->setOnhandQuantity(0);
                $layer->setIsClosed(1);
                $layer->setClosedOn($trx->getTrxDate());

                $issuedQuantity = $issuedQuantity - $consumpted_qty;
            } else {
                $consumpted_qty = $issuedQuantity;

                // deduct layer onhand
                $layer->setOnhandQuantity($on_hand - $issuedQuantity);
                $issuedQuantity = 0;
            }

            $cogs = $cogs + $consumpted_qty * $layer->getDocUnitPrice() * $layer->getExchangeRate();

            $this->getDoctrineEM()->persist($layer);

            /**
             *
             * @todo Create Layer Consumption
             */
            if ($consumpted_qty > 0) {
                $fifo_consume = new \Application\Entity\NmtInventoryFifoLayerConsume();
                $fifo_consume->setLayer($layer);

                $fifo_consume->setItem($layer->getItem());
                $fifo_consume->setQuantity($consumpted_qty);

                $fifo_consume->setDocCurrency($layer->getDocCurrency());
                $fifo_consume->setDocUnitPrice($layer->getDocUnitPrice());

                $fifo_consume->setDocTotalValue($fifo_consume->getQuantity() * $fifo_consume->getDocUnitPrice());

                $fifo_consume->setExchangeRate($layer->getExchangeRate());
                $fifo_consume->setTotalValue($fifo_consume->getDocTotalValue() * $fifo_consume->getExchangeRate());

                $fifo_consume->setInventoryTrx($trx); // important
                $fifo_consume->setCreatedOn($trx->getTrxDate());
                $fifo_consume->setCreatedBy($u);

                $fifo_consume->setToken(Rand::getString(15, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));

                $this->getDoctrineEM()->persist($fifo_consume);
            }
        }

        if ($total_onhand < $totalIssueQty) {
            $m = $this->controllerPlugin->translate('Goods Issue imposible. Issue Quantity > On-hand Quantity');
            $m = sprintf($m . ' (%s>%s)', $totalIssueQty, $total_onhand);
            throw new \Exception($m);
        }

        // Set header blocked for reversal
        $trx->setReversalBlocked(1);
        $trx->getMovement()->setReversalBlocked(1);

        return $cogs;
    }

    /**
     *
     * @deprecated
     * @param \Application\Entity\NmtInventoryTrx $trx
     * @param \Application\Entity\NmtInventoryItem $item
     * @param double $issuedQuantity
     * @param \Application\Entity\MlaUsers $u
     * @throws \Exception
     * @return number
     */
    public function valuateTrx($trx, $item, $issuedQuantity, $u)
    {
        if ($trx == null) {
            throw new \Exception("Invalid Argurment!");
        }

        if ($item == null) {
            throw new \Exception("Invalid Argurment! Item not found.");
        }

        if ($issuedQuantity == 0) {
            throw new \Exception("Nothing to valuate!");
        }

        $criteria = array(
            'isClosed' => 0,
            'item' => $item
        );

        /**
         * Important for FIFO
         */
        $sort = array(
            'postingDate' => "ASC"
        );

        $layers = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryFIFOLayer')->findBy($criteria, $sort);

        if (count($layers) == 0) {
            throw new \Exception("Goods Issue imposible. Please check the stock and the issue date.");
        }

        $cogs = 0;

        $total_onhand = 0;
        $totalIssueQty = $issuedQuantity;

        /**
         *
         * @todo Get Layer and caculate Consumption.
         */
        foreach ($layers as $layer) {
            /**@var \Application\Entity\NmtInventoryFIFOLayer $layer ;*/

            $on_hand = $layer->getOnhandQuantity();
            $total_onhand += $on_hand;

            if ($issuedQuantity == 0) {
                break;
            }

            $consumpted_qty = 0;

            if ($on_hand <= $issuedQuantity) {

                // create comsuption of all, close this layer
                $consumpted_qty = $on_hand;

                $layer->setOnhandQuantity(0);
                $layer->setIsClosed(1);
                $layer->setClosedOn($trx->getTrxDate());

                $issuedQuantity = $issuedQuantity - $consumpted_qty;
            } else {
                $consumpted_qty = $issuedQuantity;
                $layer->setOnhandQuantity($on_hand - $issuedQuantity);
                $issuedQuantity = 0;
            }

            $cogs = $cogs + $consumpted_qty * $layer->getDocUnitPrice() * $layer->getExchangeRate();

            $this->getDoctrineEM()->persist($layer);

            /**
             *
             * @todo Create Layer Consumption
             */
            if ($consumpted_qty > 0) {
                $fifo_consume = new \Application\Entity\NmtInventoryFifoLayerConsume();
                $fifo_consume->setLayer($layer);

                $fifo_consume->setItem($layer->getItem());
                $fifo_consume->setQuantity($consumpted_qty);
                $fifo_consume->setDocUnitPrice($layer->getDocUnitPrice());
                $fifo_consume->setDocTotalValue($fifo_consume->getQuantity() * $fifo_consume->getDocUnitPrice());

                $fifo_consume->setExchangeRate($layer->getExchangeRate());
                $fifo_consume->setTotalValue($fifo_consume->getDocTotalValue() * $fifo_consume->getExchangeRate());

                $fifo_consume->setInventoryTrx($trx);
                $fifo_consume->setCreatedOn($trx->getTrxDate());
                $fifo_consume->setCreatedBy($u);

                $trx->setToken(Rand::getString(10, \Application\Model\Constants::CHAR_LIST, true) . "_" . Rand::getString(21, \Application\Model\Constants::CHAR_LIST, true));

                $this->getDoctrineEM()->persist($fifo_consume);
            }
        }

        if ($total_onhand < $totalIssueQty) {
            $m = $this->controllerPlugin->translate('Goods Issue imposible. Issue Quantity > On-hand Quantity');
            $m = sprintf($m . ' (%s>%s)', $totalIssueQty, $total_onhand);
            throw new \Exception($m);
        }
        
        return $cogs;
    }
    
}

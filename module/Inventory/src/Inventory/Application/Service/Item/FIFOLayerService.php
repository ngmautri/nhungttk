<?php
namespace Inventory\Application\Service\Item;

use Application\Service\AbstractService;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Inventory\Domain\Service\ValuationServiceInterface;
use Inventory\Domain\Warehouse\Transaction\GenericTransaction;
use Zend\Math\Rand;
use Inventory\Domain\Warehouse\Transaction\TransactionRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FIFOLayerService extends AbstractService implements ValuationServiceInterface
{

    /**
     *
     * @param GenericTransaction $trx
     * @param TransactionRow $row
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\FIFOLayerServiceInterface::calculateCOGS()
     */
    public function calculateCOGS($trx,  $row)
    {
        $cogs = 0;
        if ($this->getDoctrineEM() == null || $trx == null || $row == null)
            return $cogs;
        
        /**@var \Application\Entity\MlaUsers $u ;*/
        $u = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->findOneBy(array(
            'id' => $trx->getCreatedBy()
        ));

        $sql = "SELECT * FROM nmt_inventory_fifo_layer WHERE 1 %s ORDER BY nmt_inventory_fifo_layer.posting_date ASC";

        $sql1 = sprintf("AND nmt_inventory_fifo_layer.posting_date <='%s'
AND nmt_inventory_fifo_layer.is_closed=0
AND nmt_inventory_fifo_layer.item_id=%s
AND nmt_inventory_fifo_layer.warehouse_id=%s", $trx->getMovementDate(), $row->getItem(), $trx->getWarehouse());

        $sql = sprintf($sql, $sql1);

        $rsm = new ResultSetMappingBuilder($this->doctrineEM);
        $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtInventoryFIFOLayer', 'nmt_inventory_fifo_layer');
        $query = $this->doctrineEM->createNativeQuery($sql, $rsm);
        $layers = $query->getResult();

        if (count($layers) == 0) {
            $m = $this->controllerPlugin->translate("Goods Issue imposible. Please check the stock quantity and the issue date");
            throw new \Exception($m);
        }

        $issuedQuantity = $row->getDocQuantity();

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
                $layer->setClosedOn(new \DateTime($trx->getMovementDate()));

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

                // $fifo_consume->setInventoryTrx($trx); // important
                $fifo_consume->setCreatedOn(new \DateTime($trx->getMovementDate()));
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
        // $trx->setReversalBlocked(1);
        // $trx->getMovement()->setReversalBlocked(1);

        return $cogs;
    }
}

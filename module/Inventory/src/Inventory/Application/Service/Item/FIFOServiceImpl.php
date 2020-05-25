<?php
namespace Inventory\Application\Service\Item;

use Application\Service\AbstractService;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Inventory\Domain\Exception\InvalidArgumentException;
use Inventory\Domain\Service\Contracts\FIFOServiceInterface;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;
use Ramsey;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FIFOServiceImpl extends AbstractService implements FIFOServiceInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Contracts\FIFOServiceInterface::calculateCOGS()
     */
    public function calculateCOGS(GenericTrx $trx, TrxRow $row)
    {
        $cogs = 0;
        if ($this->getDoctrineEM() == null || $trx == null || $row == null) {
            return $cogs;
        }

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

                $fifo_consume->setToken(Ramsey\Uuid\Uuid::uuid4()->toString());
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

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Contracts\FIFOServiceInterface::createLayer()
     */
    public function createLayer(GenericTrx $trx, TrxRow $row)
    {
        if ($trx == null) {
            throw new InvalidArgumentException("Transaction not found");
        }

        if ($row == null) {
            throw new InvalidArgumentException("Transaction row not found");
        }

        // Create new FIFO.
        /**
         *
         * @todo: Create FIFO Layer
         */
        $fifoLayer = new \Application\Entity\NmtInventoryFifoLayer();

        $fifoLayer->setIsClosed(0);
        $fifoLayer->setIsOpenBalance(1);

        if ($row->getItem() > 0) {

            /**
             *
             * @var \Application\Entity\NmtInventoryItem $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($row->getItem());
            $fifoLayer->setItem($obj);
        }

        if ($row->getCreatedBy() > 0) {

            /**
             *
             * @var \Application\Entity\MlaUsers $obj ;
             */
            $obj = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->find($row->getCreatedBy());
            $fifoLayer->setCreatedBy($obj);
        }

        $fifoLayer->setQuantity($row->getQuantity());

        // will be changed uppon inventory transaction.
        $fifoLayer->setOnhandQuantity($row->getQuantity());
        $fifoLayer->setDocUnitPrice($row->getUnitPrice());
        $fifoLayer->setLocalCurrency($row->getCurrency());
        $fifoLayer->setExchangeRate($row->getExchangeRate());
        $fifoLayer->setSourceClass(get_class($row));
        $fifoLayer->setSourceId($row->getID());
        $fifoLayer->setSourceToken($$row->getToken());

        $fifoLayer->setPostingDate($trx->getPostingDate());
        $fifoLayer->setCreatedOn($row->getCreatedOn());

        $fifoLayer->setToken(Ramsey\Uuid\Uuid::uuid4()->toString());
        $fifoLayer->setRemarks("Opening Balance");

        $this->doctrineEM->persist($fifoLayer);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Contracts\FIFOServiceInterface::closeLayers()
     */
    public function closeLayers(GenericTrx $trx, TrxRow $row)
    {
        $criteria = array(
            'item' => $row->getItem()
        );

        $layers = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryFifoLayer')->findBy($criteria);
        if (count($layers > 0)) {

            foreach ($layers as $l) {
                /** @var \Application\Entity\NmtInventoryFifoLayer $l ; */
                $l->setIsClosed(1);

                // @todo
                $l->setClosedOn();
                $this->getDoctrineEM()->persist($l);
            }
        }
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Contracts\FIFOServiceInterface::createLayersFor()
     */
    public function createLayersFor(GenericTrx $trx)
    {
        if ($trx == null) {
            throw new InvalidArgumentException("GenericTrx not found");
        }

        $rows = $trx->getDocRows();

        if (count($rows) == 0) {
            throw new InvalidArgumentException("GenericTrx have no lines");
        }

        foreach ($rows as $row) {

            $fifoLayer = new \Application\Entity\NmtInventoryFifoLayer();

            $fifoLayer->setIsClosed(0);
            $fifoLayer->setIsOpenBalance(1);

            /**
             *
             * @var TrxRow $row
             */

            if ($row->getItem() > 0) {

                /**
                 *
                 * @var \Application\Entity\NmtInventoryItem $obj ;
                 */
                $obj = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryItem')->find($row->getItem());
                $fifoLayer->setItem($obj);
            }

            if ($row->getWarehouse() > 0) {

                /**
                 *
                 * @var \Application\Entity\NmtInventoryWarehouse $obj ;
                 */
                $obj = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($row->getItem());
                $fifoLayer->setWarehouse($obj);
            }

            if ($row->getCreatedBy() > 0) {

                /**
                 *
                 * @var \Application\Entity\MlaUsers $obj ;
                 */
                $obj = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->find($row->getCreatedBy());
                $fifoLayer->setCreatedBy($obj);
            }

            $fifoLayer->setQuantity($row->getQuantity());

            // will be changed uppon inventory transaction.
            $fifoLayer->setOnhandQuantity($row->getQuantity());
            $fifoLayer->setDocUnitPrice($row->getUnitPrice());
            $fifoLayer->setLocalCurrency($row->getCurrency());
            $fifoLayer->setExchangeRate($row->getExchangeRate());
            $fifoLayer->setSourceClass(get_class($row));
            $fifoLayer->setSourceId($row->getId());
            $fifoLayer->setSourceToken($row->getToken());

            if ($trx->getPostingDate()) {
                $fifoLayer->setPostingDate(new \DateTime($trx->getPostingDate()));
            }

            if ($trx->getCreatedOn()) {
                $fifoLayer->setCreatedOn(new \DateTime($trx->getCreatedOn()));
            }

            $fifoLayer->setToken(Ramsey\Uuid\Uuid::uuid4()->toString());
            $fifoLayer->setRemarks(\sprintf("WH-GR from PO-GR %s", $trx->getSysNumber()));

            $this->getDoctrineEM()->persist($fifoLayer);
        }

        $this->getDoctrineEM()->flush();
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Service\Contracts\FIFOServiceInterface::closeLayersOf()
     */
    public function closeLayersOf(GenericTrx $trx)
    {
        if ($trx == null) {
            throw new InvalidArgumentException("Transaction not found");
        }

        $rows = $trx->getRows();

        if (count($rows) == 0) {
            throw new InvalidArgumentException("Transaction have no lines");
        }

        foreach ($rows as $row) {

            /** @var TrxRow $row ; */

            $criteria = array(
                'item' => $row->getItem()
            );

            $layers = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryFifoLayer')->findBy($criteria);
            if (count($layers > 0)) {

                foreach ($layers as $l) {
                    /** @var \Application\Entity\NmtInventoryFifoLayer $l ; */
                    $l->setIsClosed(1);

                    // @todo
                    $l->setClosedOn();
                    $this->getDoctrineEM()->persist($l);
                }
            }
        }

        $this->getDoctrineEM()->flush();
    }
}

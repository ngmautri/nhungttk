<?php
namespace Inventory\Application\Service\Item;

use Application\Domain\Util\Translator;
use Application\Service\AbstractService;
use Inventory\Domain\Service\Contracts\FIFOServiceInterface;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Infrastructure\Persistence\Doctrine\StockReportRepositoryImpl;
use Inventory\Infrastructure\Persistence\Filter\StockFifoLayerReportSqlFilter;
use Ramsey\Uuid\Uuid;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FIFOServiceImpl extends AbstractService implements FIFOServiceInterface
{

    public function calculateCostOfTrx(GenericTrx $trx)
    {
        $totalCost = 0;
        if ($this->getDoctrineEM() == null || $trx == null) {
            return $totalCost;
        }

        $rows = $trx->getDocRows();

        if (count($rows) == 0) {
            return $totalCost;
        }

        foreach ($rows as $row) {
            $totalCost = $totalCost + $this->calculateCOGS($trx, $row);
        }

        return $totalCost;
    }

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

        $rep = new StockReportRepositoryImpl($this->getDoctrineEM());
        $filter = new StockFifoLayerReportSqlFilter();
        $filter->setItemId($row->getItem());
        $filter->setToDate($trx->getMovementDate());
        $filter->setWarehouseId($trx->getWarehouse());
        $filter->setIsClosed(0);
        $sort_by = 'postingDate';
        $sort = 'ASC';
        $limit = null;
        $offset = null;
        $layers = $rep->getFifoLayer($filter, $sort_by, $sort, $limit, $offset);

        if (count($layers) == 0) {
            $m = Translator::translate("Goods Issue imposible. Please check the stock quantity and the issue date");
            throw new \RuntimeException($m);
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

            // unit price is converted standard price
            $cogs = $cogs + $consumpted_qty * $layer->getUnitPrice() * $layer->getExchangeRate();

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

                $fifo_consume->setToken(Uuid::uuid4()->toString());
                $this->getDoctrineEM()->persist($fifo_consume);
                $this->logInfo(sprintf("row #%s- consume layer #%s quantity %s*%s \n", $row->getId(), $layer->getId(), $consumpted_qty, $layer->getDocUnitPrice()));
            }
        }

        if ($total_onhand < $totalIssueQty) {
            $m = $this->controllerPlugin->translate('Goods Issue imposible. Issue Quantity > On-hand Quantity');
            $m = sprintf($m . ' (%s>%s)', $totalIssueQty, $total_onhand);
            $this->logAlert(sprintf("row #%s-  quantity %s=>cost %s \n", $row->getId(), $row->getDocQuantity(), $cogs));
            throw new \Exception($m);
        }

        if ($cogs < 0) {
            $m = sprintf('Cost is not valid! Cost=%s Quantity=%s Onhand=%s)', $cogs, $totalIssueQty, $total_onhand);
            $this->logAlert($m);
            throw new \Exception($m);
        }

        $this->logInfo(sprintf("Row #%s - Quantity %s=>cost %s \n", $row->getId(), $row->getDocQuantity(), $cogs));
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
        $fifoLayer->setSourceId($row->getId());
        $fifoLayer->setSourceToken($$row->getToken());

        $fifoLayer->setPostingDate($trx->getPostingDate());
        $fifoLayer->setCreatedOn($row->getCreatedOn());

        $fifoLayer->setToken(Uuid::uuid4()->toString());
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
        if ($trx == null) {
            throw new InvalidArgumentException("GenericTrx not found");
        }

        $criteria = array(
            'item' => $row->getItem(),
            'warehouse' => $$trx->getWarehouse()
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
        $wh = null;
        if ($trx->getWarehouse() > 0) {

            /**
             *
             * @var \Application\Entity\NmtInventoryWarehouse $obj ;
             */
            $wh = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryWarehouse')->find($trx->getWarehouse());
        }

        $rows = $trx->getDocRows();

        if (count($rows) == 0) {
            throw new InvalidArgumentException("GenericTrx have no lines");
        }

        foreach ($rows as $row) {

            $fifoLayer = new \Application\Entity\NmtInventoryFifoLayer();
            $fifoLayer->setWarehouse($wh);

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

            if ($row->getCreatedBy() > 0) {

                /**
                 *
                 * @var \Application\Entity\MlaUsers $obj ;
                 */
                $obj = $this->doctrineEM->getRepository('Application\Entity\MlaUsers')->find($row->getCreatedBy());
                $fifoLayer->setCreatedBy($obj);
            }

            $fifoLayer->setExchangeRate($trx->getExchangeRate()); // get from root.

            // get from root
            if ($trx->getLocalCurrency() > 0) {

                /**
                 *
                 * @var \Application\Entity\NmtApplicationCurrency $obj ;
                 */
                $obj = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($row->getCreatedBy());
                $fifoLayer->setLocalCurrency($obj);
            }

            // get from root
            if ($trx->getDocCurrency() > 0) {

                /**
                 *
                 * @var \Application\Entity\NmtApplicationCurrency $obj ;
                 */
                $obj = $this->doctrineEM->getRepository('Application\Entity\NmtApplicationCurrency')->find($row->getCreatedBy());
                $fifoLayer->setDocCurrency($obj);
            }

            // quantity in standard unit.
            $fifoLayer->setQuantity($row->getConvertedStandardQuantity());
            $fifoLayer->setUnitPrice($row->getConvertedStandardUnitPrice());

            // will be changed uppon inventory transaction.
            $fifoLayer->setOnhandQuantity($row->getConvertedStandardQuantity());
            $fifoLayer->setDocQuantity($row->getDocQuantity());
            $fifoLayer->setStandardConvertFactor($row->getConversionFactor());
            $fifoLayer->setDocUnitPrice($row->getDocUnitPrice());

            $fifoLayer->setSourceClass(get_class($row));
            $fifoLayer->setSourceId($row->getId());
            $fifoLayer->setSourceToken($row->getToken());

            if ($trx->getPostingDate()) {
                $fifoLayer->setPostingDate(new \DateTime($trx->getPostingDate()));
            }

            if ($trx->getCreatedOn()) {
                $fifoLayer->setCreatedOn(new \DateTime($trx->getCreatedOn()));
            }

            $fifoLayer->setToken(Uuid::uuid4()->toString());
            $fifoLayer->setRemarks(\sprintf("[Auto.] Ref. %s-%s", $trx->getMovementType(), $trx->getSysNumber()));

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

        $rows = $trx->getDocRows();

        if (count($rows) == 0) {
            throw new InvalidArgumentException("Transaction have no lines");
        }

        foreach ($rows as $row) {

            /** @var TrxRow $row ; */

            $criteria = array(
                'item' => $row->getItem(),
                'warehouse' => $trx->getWarehouse()
            );

            $layers = $this->doctrineEM->getRepository('Application\Entity\NmtInventoryFifoLayer')->findBy($criteria);
            if (count($layers > 0)) {

                foreach ($layers as $l) {
                    /** @var \Application\Entity\NmtInventoryFifoLayer $l ; */
                    $l->setIsClosed(1);
                    $l->setClosedOn(new \DateTime($trx->getMovementDate()));
                    $this->getDoctrineEM()->persist($l);
                }
            }
        }

        $this->getDoctrineEM()->flush();
    }
}

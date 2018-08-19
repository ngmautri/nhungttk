<?php
namespace Inventory\Service;

use Application\Service\AbstractService;
use Zend\Math\Rand;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class FIFOLayerService extends AbstractService
{

    /**
     * Valuation
     *
     * @param \Application\Entity\NmtInventoryItem $item
     * @param double $issuedQuantity
     * @param \DateTime $transactionDate
     */
    public function valuate(\Application\Entity\NmtInventoryItem $item, $issuedQuantity, $transactionDate)
    {
        $cost = 0;

        /**
         *
         * @todo Get Layer
         */

        /**
         *
         * @todao Doing Valuation and update FIFO Layer
         */

        return $cost;
    }

    /**
     *
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
            throw new \Exception("Goods Issue imposible. No FIFO Layer found!");
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
            throw new \Exception("Goods Issue imposible! Issue Quantity > On-hand Quantity");
        }

        return $cogs;
    }

    /**
     *
     * @param \Application\Entity\NmtProcureGr $source
     * @param \Application\Entity\MlaUsers $u
     * @throws \Exception
     */
    public function createFIFOLayerFromGR(\Application\Entity\NmtProcureGr $source, \Application\Entity\MlaUsers $u)
    {
        if (! $source instanceof \Application\Entity\NmtProcureGr) {
            throw new \Exception("Source object is not valid");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("User is not valid");
        }

        // Do create
    }

    /**
     *
     * @param \Application\Entity\FinVendorInvoice $source
     * @param \Application\Entity\MlaUsers $u
     * @throws \Exception
     */
    public function createFIFOLayerFromAP(\Application\Entity\FinVendorInvoice $source, \Application\Entity\MlaUsers $u)
    {
        if (! $source instanceof \Application\Entity\NmtProcureGr) {
            throw new \Exception("Source object is not valid");
        }

        if (! $u instanceof \Application\Entity\MlaUsers) {
            throw new \Exception("User is not valid");
        }

        // Do create
    }
}

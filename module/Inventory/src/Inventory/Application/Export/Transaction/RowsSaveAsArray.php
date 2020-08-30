<?php
namespace Inventory\Application\Export\Transaction;

use Inventory\Application\Export\Transaction\Contracts\AbstractSaveAs;
use Inventory\Application\Export\Transaction\Contracts\RowsSaveAsInterface;
use Inventory\Application\Export\Transaction\Formatter\AbstractRowFormatter;
use Inventory\Domain\Transaction\TrxRowSnapshot;
use Inventory\Domain\Transaction\Contracts\TrxFlow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RowsSaveAsArray extends AbstractSaveAs implements RowsSaveAsInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Contract\RowsSaveAsInterface::saveAs()
     */
    public function saveAs($rows, AbstractRowFormatter $formatter)
    {
        try {

            if (count($rows) == 0) {
                return null;
            }

            $output = array();
            // do some calulattion here.
            $stockQty = 0;
            $stockvalue = 0;
            $warehouseId = null;

            foreach ($rows as $row) {

                /**
                 *
                 * @var TrxRowSnapshot $row ;
                 */
                if ($row->getWhLocation() != null) {
                    continue;
                }

                if ($row->getWh() != $warehouseId) {
                    $warehouseId = $row->getWh();

                    if ($row->getFlow() == TrxFlow::WH_TRANSACTION_OUT) {
                        $stockQty = $row->getQuantity() * - 1;
                        $stockvalue = $row->getCogsLocal() * - 1;
                    } elseif ($row->getFlow() == TrxFlow::WH_TRANSACTION_IN) {
                        $stockQty = $row->getQuantity();
                        $stockvalue = $row->getConvertedStandardUnitPrice() * $row->getQuantity() * $row->getExchangeRate();
                    }

                    $row->setStockQty($stockQty);
                    $row->setStockValue($stockvalue);
                } else {

                    if ($row->getFlow() == TrxFlow::WH_TRANSACTION_OUT) {
                        $stockQty = $stockQty + $row->getQuantity() * - 1;
                        $stockvalue = $stockvalue + $row->getCogsLocal() * - 1;
                    } elseif ($row->getFlow() == TrxFlow::WH_TRANSACTION_IN) {
                        $stockQty = $stockQty + $row->getQuantity();
                        $stockvalue = $stockvalue + $row->getConvertedStandardUnitPrice() * $row->getQuantity() * $row->getExchangeRate();
                    }

                    $row->setStockQty($stockQty);
                    $row->setStockValue($stockvalue);
                }

                $output[] = $formatter->format($row);
            }

            return $output;
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
        }
    }
}

<?php
namespace Inventory\Application\Export\Transaction;

use Inventory\Application\Export\Transaction\Contracts\RowsSaveAsInterface;
use Inventory\Application\Export\Transaction\Formatter\AbstractRowFormatter;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;
use Procure\Infrastructure\Mapper\PrMapper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RawRowsSaveAsArray implements RowsSaveAsInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Application\Export\Transaction\Contracts\RowsSaveAsInterface::saveAs()
     */
    public function saveAs($rows, AbstractRowFormatter $formatter)
    {
        try {

            if (count($rows) == 0) {
                return null;
            }

            $output = array();
            foreach ($rows as $r) {

                $entity = $r[0];
                $row = new PRRowSnapshot();
                $row = PrMapper::convertToRowSnapshot($entity);

                $row->draftPoQuantity = $r["po_qty"];
                $row->postedPoQuantity = $r["posted_po_qty"];

                $row->draftGrQuantity = $r["gr_qty"];
                $row->postedGrQuantity = $r["posted_gr_qty"];

                $row->draftApQuantity = $r["ap_qty"];
                $row->postedApQuantity = $r["posted_ap_qty"];

                $row->draftStockQrQuantity = $r["stock_gr_qty"];
                $row->postedStockQrQuantity = $r["posted_stock_gr_qty"];

                // $output[] = $formatter->format($row);
                $output[] = $row;
            }

            return $output;
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
        }
    }
}

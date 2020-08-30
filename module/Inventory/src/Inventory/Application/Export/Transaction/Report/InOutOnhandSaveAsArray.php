<?php
namespace Inventory\Application\Export\Transaction\Report;

use Inventory\Application\DTO\Transaction\Report\InOutOnhandDTO;
use Inventory\Application\Export\Transaction\Contracts\AbstractSaveAs;
use Inventory\Application\Export\Transaction\Contracts\RowsSaveAsInterface;
use Inventory\Application\Export\Transaction\Formatter\AbstractRowFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class InOutOnhandSaveAsArray extends AbstractSaveAs implements RowsSaveAsInterface
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

            foreach ($rows as $rowArray) {

                $this->logInfo(\json_encode($rowArray));

                $row = new InOutOnhandDTO();
                if (isset($rowArray['item_id'])) {
                    $row->item = $rowArray['item_id'];
                }
                if (isset($rowArray['begin_qty'])) {
                    $row->setBeginQty($rowArray['begin_qty']);
                }
                if (isset($rowArray['gr_qty'])) {
                    $row->setGrQty($rowArray['gr_qty']);
                }
                if (isset($rowArray['gi_qty'])) {
                    $row->setGiQty($rowArray['gi_qty']);
                }
                if (isset($rowArray['end_qty'])) {
                    $row->setEndQty($rowArray['end_qty']);
                }
                if (isset($rowArray['begin_vl'])) {
                    $row->setBeginValue($rowArray['begin_vl']);
                }
                $output[] = $formatter->format($row);
            }
            return $output;
        } catch (\Exception $e) {
            $this->logException($e);
        }
    }
}

<?php
namespace Inventory\Application\Reporting\Item\Export;

use Inventory\Application\DTO\Item\Report\ItemInOutOnhandDTO;
use Inventory\Application\Export\Item\Contracts\SaveAsInterface;
use Inventory\Application\Export\Item\Formatter\AbstractFormatter;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class InOutOnHandSaveAsArray implements SaveAsInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Application\Export\Item\Contracts\SaveAsInterface::saveAs()
     */
    public function saveAs($rows, AbstractFormatter $formatter)
    {
        try {

            if (count($rows) == 0) {
                return null;
            }

            $output = array();
            foreach ($rows as $r) {

                $row = new ItemInOutOnhandDTO();
                $row->setId($r['item_id']);
                $row->setItemType($r['item_type_id']);
                $row->setItemName($r['item_name']);
                $row->setItemSku($r['item_sku']);
                $row->setSysNumber($r['sys_number']);
                $row->setToken($r['token']);
                $row->setManufacturerCode($r['manufacturer_code']);
                $row->setManufacturerSerial($r['manufacturer_serial']);
                $row->setManufacturerModel($r['manufacturer_model']);
                $row->setWarehouseName($r['wh_name']);
                $row->setBeginQty($r['begin_qty']);
                $row->setBeginValue($r['begin_vl']);
                $row->setGrQty($r['gr_qty']);
                $row->setGrValue($r['gr_vl']);
                $row->setGiQty($r['gi_qty']);
                $row->setGiValue($r['gi_vl']);
                $row->setEndQty($r['end_qty']);
                $row->setEndValue($r['end_vl']);

                $output[] = $formatter->format($row);
            }

            return $output;
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
        }
    }
}

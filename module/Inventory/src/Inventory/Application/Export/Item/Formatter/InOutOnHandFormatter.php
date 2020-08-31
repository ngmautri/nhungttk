<?php
namespace Inventory\Application\Export\Item\Formatter;

use Inventory\Application\DTO\Item\Report\ItemInOutOnhandDTO;
use Zend\Escaper\Escaper;

/**
 * Default Row Formatter
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class InOutOnHandFormatter extends AbstractFormatter
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Formatter\AbstractRowFormatter::format()
     */
    public function format($row)
    {
        if ($row instanceof ItemInOutOnhandDTO) {
            $decimalNo = 0;
            $row->beginQty = ($row->getBeginQty() != null ? number_format($row->getBeginQty(), $decimalNo) : 0);
            $row->grQty = ($row->getGrQty() != null ? number_format($row->getGrQty(), $decimalNo) : 0);
            $row->giQty = ($row->getGiQty() != null ? number_format($row->getGiQty(), $decimalNo) : 0);
            $row->endQty = ($row->getEndQty() != null ? number_format($row->getEndQty(), $decimalNo) : 0);
            $row->beginValue = ($row->getBeginValue() != null ? number_format($row->getBeginValue(), $decimalNo) : 0);
            $row->grValue = ($row->getGrValue() != null ? number_format($row->getGrValue(), $decimalNo) : 0);
            $row->giValue = ($row->getGiValue() != null ? number_format($row->getGiValue(), $decimalNo) : 0);
            $row->endValue = ($row->getEndValue() != null ? number_format($row->getEndValue(), $decimalNo) : 0);

            $escaper = new Escaper();

            $f = "/inventory/item/show1?token=%s&entity_id=%s";
            $item_detail = sprintf($f, $row->getToken(), $row->getId());

            $f = "showJqueryDialog('Detail of Item: %s','1600',$(window).height()-50,'%s','j_loaded_data', true);";
            $onclick = sprintf($f, $escaper->escapeJs($row->getItemName()), $item_detail);

            if (strlen($row->getItemName()) < 35) {
                $f = '%s <a style="cursor:pointer;color:#337ab7"  item-pic="" id="%s" item_name="%s" title="%s" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>';
                $row->itemName = sprintf($f, $row->itemName, $row->id, $row->itemName, $row->itemName, $onclick);
            } else {
                $f = '%s <a style="cursor:pointer;color:#337ab7"  item-pic="" id="%s" item_name="%s" title="%s" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>';
                $row->itemName = sprintf($f, substr($row->itemName, 0, 30), $row->id, $row->itemName, $row->itemName, $onclick);
            }
        }

        return $row;
    }
}

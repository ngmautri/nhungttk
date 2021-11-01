<?php
namespace Inventory\Application\Reporting\ItemSerial\Export\Formatter;

use Application\Domain\Util\Collection\Formatter\AbstractElementFormatter;
use Inventory\Domain\Item\Serial\SerialSnapshot;
use Zend\Escaper\Escaper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultItemSerialFormatter extends AbstractElementFormatter
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Collection\Contracts\ElementFormatterInterface::format()
     */
    public function format($element)
    {
        if (! $element instanceof SerialSnapshot) {
            return null;
        }

        $escaper = new Escaper();

        $onclick = null;
        if ($element->itemName != null) {
            $item_detail = sprintf("/inventory/item/show1?token=%s&entity_id=%s", $element->getItemToken(), $element->getItem());

            $f = "showJqueryDialog('Detail of Item: %s','1600',$(window).height()-50,'%s','j_loaded_data', true);";
            $onclick = sprintf($f, $escaper->escapeJs($element->getItemName()), $element, $item_detail);
        }

        if (strlen($element->getItemName()) < 35) {
            $f = '%s <a style="cursor:pointer;color:#337ab7"  item-pic="" id="%s" item_name="%s" title="%s" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>';
            $element->itemName = sprintf($f, $element->getItem(), $element->getItem, $element->getItemName(), $element->getItemName(), $onclick);
        } else {
            $f = '%s <a style="cursor:pointer;color:#337ab7"  item-pic="" id="%s" item_name="%s" title="%s" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>';
            $element->itemName = sprintf($f, substr($element->itemName, 0, 30), $element, $element->itemName, $element->itemName, $onclick);
        }

        return $element;
    }
}


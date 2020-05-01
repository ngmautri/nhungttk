<?php
namespace Procure\Application\Service\Output\Formatter;

use Procure\Domain\RowSnapshot;
use Zend\Escaper\Escaper;

/**
 * Default Row Formatter
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultRowFormatter extends AbstractRowFormatter
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Formatter\AbstractRowFormatter::format()
     */
    public function format(RowSnapshot $row)
    {
        if (! $row instanceof RowSnapshot) {
            return null;
        }

        $decimalNo = 0;
        $curency = array(
            "USD",
            "THB",
            "EUR",
            "DKK"
        );

        if (in_array($row->getDocCurrencyISO(), $curency)) {
            $decimalNo = 2;
        }

        $escaper = new Escaper();

        $onclick = null;
        if ($row->itemName !== null) {
            $item_detail = sprintf("/inventory/item/show1?token=%s&entity_id=%s", $row->getItemToken(), $row->getItem());
            $onclick = sprintf("showJqueryDialog('Detail of Item: %s','1600',$(window).height()-50,'%s','j_loaded_data', true);", $escaper->escapeJs($row->getItemName()), $item_detail);
        }

        if (strlen($row->getItemName()) < 35) {
            $row->itemName = sprintf('%s <a style="cursor:pointer;color:#337ab7"  item-pic="" id="%s" item_name="%s" title="%s" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>', $row->itemName, $row->item, $row->itemName, $row->itemName, $onclick);
        } else {

            $row->itemName = sprintf('%s <a style="cursor:pointer;color:#337ab7"  item-pic="" id="%s" item_name="%s" title="%s" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>', substr($row->itemName, 0, 30), $row->item, $row->itemName, $row->itemName, $onclick);
        }

        if ($row->docUnitPrice !== null) {
            $row->docUnitPrice = number_format($row->docUnitPrice, $decimalNo);
        }

        if ($row->netAmount !== null) {
            $row->netAmount = number_format($row->netAmount, $decimalNo);
        }

        if ($row->taxAmount !== null) {
            $row->taxAmount = number_format($row->taxAmount, $decimalNo);
        }
        if ($row->grossAmount !== null) {
            $row->grossAmount = number_format($row->grossAmount, $decimalNo);
        }

        if ($row->convertedStandardQuantity !== null) {
            $row->convertedStandardQuantity = number_format($row->convertedStandardQuantity, $decimalNo);
        }

        if ($row->convertedStandardUnitPrice !== null) {
            $row->convertedStandardUnitPrice = number_format($row->convertedStandardUnitPrice, $decimalNo);
        }

        $link = sprintf('<a style="cursor:pointer;color:#337ab7" title="%s" target="_blank" href="/procure/pr/show?token=%s&entity_id=%s&checkum=%s">&nbsp;&nbsp;(i)&nbsp;</a>', $row->prRowIndentifer, $row->prToken, $row->pr, $row->prChecksum);

        if ($row->prNumber !== null) {
            $row->prNumber = sprintf('<span style="font-size:8pt; color: graytext">%s %s</span', $row->prNumber, $link);
        }

        $row->vendorItemName = sprintf('<span style="font-size:8pt; color: graytext">%s</span>', $row->getVendorItemName());
        $row->vendorItemCode = sprintf('<span style="font-size:8pt; color: graytext">%s</span>', $row->getVendorItemCode());

        return $row;
    }
}

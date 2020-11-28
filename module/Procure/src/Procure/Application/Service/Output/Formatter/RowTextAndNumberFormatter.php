<?php
namespace Procure\Application\Service\Output\Formatter;

use Application\Domain\Shared\Number\NumberFormatter;
use Procure\Domain\RowSnapshot;
use Zend\Escaper\Escaper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class RowTextAndNumberFormatter extends AbstractRowFormatter
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

        $item_detail = sprintf("/inventory/item/show1?token=%s&checksum=%s&entity_id=%s", $row->getItemToken(), $row->getItemChecksum(), $row->getItem());
        $onclick = sprintf("showJqueryDialog('Detail of Item: %s','1600',$(window).height()-50,'%s','j_loaded_data', true);", $escaper->escapeJs($row->getItemName()), $item_detail);

        if (strlen($row->getItemName()) < 35) {
            $row->itemName = sprintf('%s <a style="cursor:pointer;color:#337ab7"  item-pic="" id="%s" item_name="%s" title="%s" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>', $row->itemName, $row->item, $row->itemName, $row->itemName, $onclick);
        } else {

            $row->itemName = sprintf('%s <a style="cursor:pointer;color:#337ab7"  item-pic="" id="%s" item_name="%s" title="%s" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>', substr($row->itemName, 0, 30), $row->item, $row->itemName, $row->itemName, $onclick);
        }

        if ($row->docUnitPrice !== null) {
            $row->docUnitPrice = NumberFormatter::format($row->docUnitPrice, $this->getLocale(), $decimalNo, $decimalNo);
        }

        if ($row->netAmount !== null) {
            $row->netAmount = NumberFormatter::format($row->netAmount, $this->getLocale(), $decimalNo, $decimalNo);
        }

        if ($row->taxAmount !== null) {
            $row->taxAmount = NumberFormatter::format($row->taxAmount, $this->getLocale(), $decimalNo, $decimalNo);
        }
        if ($row->grossAmount !== null) {
            $row->grossAmount = NumberFormatter::format($row->grossAmount, $this->getLocale(), $decimalNo, $decimalNo);
        }

        if ($row->convertedStandardQuantity !== null) {
            $row->convertedStandardQuantity = NumberFormatter::format($row->convertedStandardQuantity, $this->getLocale(), $decimalNo, $decimalNo);
        }

        if ($row->convertedStandardUnitPrice !== null) {
            $row->convertedStandardUnitPrice = NumberFormatter::format($row->convertedStandardUnitPrice, $this->getLocale(), $decimalNo, $decimalNo);
        }

        $link = sprintf('<a style="cursor:pointer;color:#337ab7" title="%s" target="_blank" href="/procure/pr/view?entity_token=%s&entity_id=%s">&nbsp;&nbsp;(i)&nbsp;</a>', $row->prRowIndentifer, $row->prToken, $row->pr);

        if ($row->prNumber != null) {
            $row->prNumber = sprintf('<span style="font-size:8pt; color: graytext">%s %s</span>', $row->prNumber, $link);
        }

        /*
         * if ($row->prNumber != null) {
         * $f = '/procure/pr/view1?&entity_id=%s';
         * $prViewUrl = \sprintf($f, $row->getPr());
         *
         * $f = "showJqueryDialog('PR Row #%s','1800',$(window).height()-50,'%s','j_loaded_data', true);";
         * $onclick = sprintf($f, $row->prNumber, $prViewUrl);
         *
         * $f = '%s <a style="cursor:pointer;color:#337ab7" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>';
         * $row->prNumber = \sprintf($f, $row->prNumber, $onclick);
         * }
         */
        $row->vendorItemName = sprintf('<span style="font-size:8pt; color: graytext">%s</span>', $row->getVendorItemName());
        $row->vendorItemCode = sprintf('<span style="font-size:8pt; color: graytext">%s</span>', $row->getVendorItemCode());
        $row->itemManufacturerModel = sprintf('<span style="font-size:8pt; color: graytext">%s</span>', $row->getItemManufacturerModel());

        return $row;
    }
}

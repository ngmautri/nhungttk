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
        $escaper = new Escaper();

        $item_detail = sprintf("/inventory/item/show1?token=%s&checksum=%s&entity_id=%s", $row->getItemToken(), $row->getItemChecksum(), $row->getItem());
        $onclick = sprintf("showJqueryDialog('Detail of Item: %s','1600',$(window).height()-50,'%s','j_loaded_data', true);", $escaper->escapeJs($row->getItemName()), $item_detail);

        if (strlen($row->getItemName()) < 35) {
            $row->itemName = sprintf('%s <a style="cursor:pointer;color:#337ab7"  item-pic="" id="%s" item_name="%s" title="%s" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>', $row->itemName, $row->item, $row->itemName, $row->itemName, $onclick);
        } else {

            $row->itemName = sprintf('%s <a style="cursor:pointer;color:#337ab7"  item-pic="" id="%s" item_name="%s" title="%s" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>', substr($row->itemName, 0, 30), $row->item, $row->itemName, $row->itemName, $onclick);
        }

        // format money
        $row->docUnitPrice = NumberFormatter::formatMoneyNumberForGrid($row->docUnitPrice, $row->getDocCurrencyISO(), $this->getLocale());
        $row->unitPrice = NumberFormatter::formatMoneyNumberForGrid($row->unitPrice, $row->getDocCurrencyISO(), $this->getLocale());
        $row->netAmount = NumberFormatter::formatMoneyNumberForGrid($row->netAmount, $row->getDocCurrencyISO(), $this->getLocale());
        $row->taxAmount = NumberFormatter::formatMoneyNumberForGrid($row->taxAmount, $row->getDocCurrencyISO(), $this->getLocale());
        $row->grossAmount = NumberFormatter::formatMoneyNumberForGrid($row->grossAmount, $row->getDocCurrencyISO(), $this->getLocale());
        $row->convertedStandardUnitPrice = NumberFormatter::formatMoneyNumberForGrid($row->convertedStandardUnitPrice, $row->getDocCurrencyISO(), $this->getLocale());
        $row->localUnitPrice = NumberFormatter::formatMoneyNumberForGrid($row->localUnitPrice, $row->getLocalCurrencyISO(), $this->getLocale());

        // format number
        $row->docQuantity = NumberFormatter::formatNumberForGrid($row->docQuantity, $this->getLocale());
        $row->convertedStandardQuantity = NumberFormatter::formatNumberForGrid($row->convertedStandardQuantity, $this->getLocale());
        $row->standardConvertFactor = NumberFormatter::formatNumberForGrid($row->standardConvertFactor, $this->getLocale());

        $link = sprintf('<a style="cursor:pointer;color:#337ab7" title="%s" target="_blank" href="/procure/pr/view?entity_token=%s&entity_id=%s">&nbsp;&nbsp;(i)&nbsp;</a>', $row->prRowIndentifer, $row->prToken, $row->pr);

        if ($row->prNumber != null) {
            $row->prNumber = sprintf('<span style="font-size:8pt; color: graytext">%s %s</span>', $row->prNumber, $link);
        }

        $f = '<a style="cursor:pointer;color:#337ab7" title="%s" target="_blank" href="/procure/pr/view?entity_token=%s&entity_id=%s&checkum=%s">&nbsp;&nbsp;(i)&nbsp;</a>';

        $link = sprintf($f, $row->rowIdentifer, $row->prToken, $row->pr, $row->prChecksum);

        if ($row->docNumber !== null) {
            $row->docNumber = sprintf('<span style="font-size:8pt; color: graytext">%s %s</span', $row->docNumber, $link);
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

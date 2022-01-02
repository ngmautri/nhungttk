<?php
namespace Procure\Application\Service\Render\Formatter;

use Application\Domain\Shared\Number\NumberFormatter;
use Application\Domain\Util\Collection\Formatter\AbstractElementFormatter;
use Procure\Domain\RowSnapshot;
use Zend\Escaper\Escaper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RowTextAndNumberFormatter extends AbstractElementFormatter
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Collection\Contracts\ElementFormatterInterface::format()
     */
    public function format($element)
    {
        if (! $element instanceof RowSnapshot) {
            return null;
        }
        $escaper = new Escaper();

        $item_detail = sprintf("/inventory/item/show1?token=%s&checksum=%s&entity_id=%s", $element->getItemToken(), $element->getItemChecksum(), $element->getItem());
        $onclick = sprintf("showJqueryDialog('Detail of Item: %s','1600',$(window).height()-50,'%s','j_loaded_data', true);", $escaper->escapeJs($element->getItemName()), $item_detail);

        if (strlen($element->getItemName()) < 35) {
            $element->itemName = sprintf('%s <a style="cursor:pointer;color:#337ab7"  item-pic="" id="%s" item_name="%s" title="%s" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>', $element->itemName, $element->item, $element->itemName, $element->itemName, $onclick);
        } else {

            $element->itemName = sprintf('%s <a style="cursor:pointer;color:#337ab7"  item-pic="" id="%s" item_name="%s" title="%s" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>', substr($element->itemName, 0, 30), $element->item, $element->itemName, $element->itemName, $onclick);
        }

        // format money
        $element->docUnitPrice = NumberFormatter::formatMoneyNumberForGrid($element->docUnitPrice, $element->getDocCurrencyISO(), $this->getLocale());
        $element->unitPrice = NumberFormatter::formatMoneyNumberForGrid($element->unitPrice, $element->getDocCurrencyISO(), $this->getLocale());
        $element->netAmount = NumberFormatter::formatMoneyNumberForGrid($element->netAmount, $element->getDocCurrencyISO(), $this->getLocale());
        $element->taxAmount = NumberFormatter::formatMoneyNumberForGrid($element->taxAmount, $element->getDocCurrencyISO(), $this->getLocale());
        $element->grossAmount = NumberFormatter::formatMoneyNumberForGrid($element->grossAmount, $element->getDocCurrencyISO(), $this->getLocale());
        $element->convertedStandardUnitPrice = NumberFormatter::formatMoneyNumberForGrid($element->convertedStandardUnitPrice, $element->getDocCurrencyISO(), $this->getLocale());
        $element->localUnitPrice = NumberFormatter::formatMoneyNumberForGrid($element->localUnitPrice, $element->getLocalCurrencyISO(), $this->getLocale());

        // format number qty
        $element->docQuantity = NumberFormatter::formatNumberForGrid($element->getDocQuantity(), $this->getLocale());
        $element->convertedStandardQuantity = NumberFormatter::formatNumberForGrid($element->getConvertedStandardQuantity(), $this->getLocale());
        $element->standardConvertFactor = NumberFormatter::formatNumberForGrid($element->standardConvertFactor, $this->getLocale());

        $link = sprintf('<a style="cursor:pointer;color:#337ab7" title="%s" target="_blank" href="/procure/pr/view?entity_token=%s&entity_id=%s">&nbsp;&nbsp;(i)&nbsp;</a>', $element->prRowIndentifer, $element->prToken, $element->pr);

        if ($element->prNumber != null) {
            $element->prNumber = sprintf('<span style="font-size:8pt; color: graytext">%s %s</span>', $element->prNumber, $link);
        }

        $f = '<a style="cursor:pointer;color:#337ab7" title="%s" target="_blank" href="/procure/pr/view?entity_token=%s&entity_id=%s&checkum=%s">&nbsp;&nbsp;(i)&nbsp;</a>';

        $link = sprintf($f, $element->rowIdentifer, $element->prToken, $element->pr, $element->prChecksum);

        if ($element->docNumber !== null) {
            $element->docNumber = sprintf('<span style="font-size:8pt; color: graytext">%s %s</span', $element->docNumber, $link);
        }
        $element->vendorItemName = sprintf('<span style="font-size:8pt; color: graytext">%s</span>', $element->getVendorItemName());
        $element->vendorItemCode = sprintf('<span style="font-size:8pt; color: graytext">%s</span>', $element->getVendorItemCode());
        $element->itemManufacturerModel = sprintf('<span style="font-size:8pt; color: graytext">%s</span>', $element->getItemManufacturerModel());

        return $element;
    }
}

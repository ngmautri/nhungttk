<?php
namespace Procure\Application\Reporting\PO\Output;

use Procure\Application\Reporting\PO\Output\PoRowStatusOutputStrategy;
use Procure\Domain\PurchaseOrder\PORowDetailsSnapshot;
use Zend\Escaper\Escaper;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoRowStatusInArray extends PoRowStatusOutputStrategy
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Reporting\PR\Output\PrRowStatusOutputStrategy::createOutput()
     */
    public function createOutput($result)
    {
        if (count($result) == 0) {
            return null;
        }

        $output = array();

        foreach ($result as $a) {
            /**@var PORowDetailsSnapshot $a ;*/

            $decimalNo = 0;
            $curency = array(
                "USD",
                "THB",
                "EUR"
            );

            if (in_array($a->docCurrencyISO, $curency)) {
                $decimalNo = 2;
            }

            if ($a->unitPrice!==null) {
                $a->unitPrice = number_format($a->unitPrice, $decimalNo);
            }

            if ($a->billedAmount!==null) {
                $a->billedAmount = number_format($a->billedAmount, $decimalNo);
            }

            if ($a->netAmount!==null) {
                $a->netAmount = number_format($a->netAmount, $decimalNo);
            }

            if ($a->openAPAmount!==null) {
                $a->openAPAmount = number_format($a->openAPAmount, $decimalNo);
            }

            if ($a->draftAPQuantity!==null) {
                $a->draftAPQuantity = number_format($a->draftAPQuantity, 0);
            }

            if ($a->draftGrQuantity!==null) {
                $a->draftGrQuantity = number_format($a->draftGrQuantity, 0);
            }

            if ($a->postedAPQuantity!==null) {
                $a->postedAPQuantity = number_format($a->postedAPQuantity, 0);
            }

            if ($a->postedGrQuantity!==null) {
                $a->postedGrQuantity = number_format($a->postedGrQuantity, 0);
            }

            $escaper = new Escaper();

            $item_detail = sprintf("/inventory/item/show1?token=%s&checksum=%s&entity_id=%s", $a->itemToken, $a->itemChecksum, $a->item);
            $onclick = sprintf("showJqueryDialog('Detail of Item: %s','1600',$(window).height()-50,'%s','j_loaded_data', true);", $escaper->escapeJs($a->itemName), $item_detail);
            
            
            $item_link = sprintf('<a style="cursor:pointer;color:#337ab7"  item-pic="" id="%s" item_name="%s" title="%s" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>',
                $a->item, $escaper->escapeJs($a->itemName), $escaper->escapeJs($a->itemName), $onclick);
            
            
            if (strlen($a->itemName) < 35) {

                $a->itemName = $a->itemName. $item_link;
            } else {
     
                $a->itemName = substr($a->itemName, 0, 30). $item_link;
            }

            
            $a->vendorItemName= sprintf('<span style="font-size:8pt; color: graytext">%s</span>', $a->vendorItemName);
            $a->vendorItemCode= sprintf('<span style="font-size:8pt; color: graytext">%s</span>', $a->vendorItemCode);
            
            $po_link = sprintf('<a style="cursor:pointer;color:#337ab7" title="%s" target="_blank" href="/procure/po/view?token=%s&entity_id=%s">&nbsp;&nbsp;(i)&nbsp;</a>', 
                $a->rowIdentifer, $a->poToken, $a->po);
            
            $a->poNumber = $a->poNumber . $po_link;
            $output[] = $a;
        }

        return $output;
    }
}

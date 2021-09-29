<?php
namespace Procure\Application\Service\PR\Output;

use Procure\Application\Service\Output\Formatter\RowFormatterDecorator;
use Procure\Domain\RowSnapshot;
use Procure\Domain\Contracts\ProcureTrxStatus;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;

/**
 * RR Row Output.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrRowFormatter extends RowFormatterDecorator
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
            "EUR"
        );

        if (in_array($row->getDocCurrencyISO(), $curency)) {
            $decimalNo = 2;
        }

        $row = $this->formatter->format($row);

        $zero = \sprintf('<span style="color:white;">0</span>');
        // $zero = 0;
        // then decorate
        if ($row instanceof PRRowSnapshot) {

            switch ($row->getTransactionStatus()) {
                case ProcureTrxStatus::UNCOMPLETED:
                    $row->transactionStatus = \sprintf('&nbsp;<span  class="label label-warning">.</span><span style="font-weight: normal;"> %s</span>', 'pending');
                    break;

                case ProcureTrxStatus::HAS_QUOTATION:
                    $row->transactionStatus = \sprintf('&nbsp;<span style="color:navy;">%s</span> ', "Quoted");
                    break;

                case ProcureTrxStatus::COMPLETED:
                    $row->transactionStatus = \sprintf('&nbsp;<span style="color:graytext;">%s</span> ', "Done");
                    break;
                case ProcureTrxStatus::COMMITTED:
                    $row->transactionStatus = \sprintf('&nbsp;<span style="color:green;">%s</span> ', "Committed");
                    break;
                case ProcureTrxStatus::PARTIAL_COMMITTED:
                    $row->transactionStatus = \sprintf('&nbsp;<span style="color:green;">%s</span> ', "Parial committed");
                    break;
            }

            /*
             * $escaper = new Escaper();
             *
             * $onclick = null;
             * if ($row->itemName !== null) {
             * $item_detail = sprintf("/inventory/item/show1?token=%s&entity_id=%s", $row->getItemToken(), $row->getItem());
             * $onclick = sprintf("showJqueryDialog('Detail of Item: %s','1600',$(window).height()-50,'%s','j_loaded_data', true);", $escaper->escapeJs($row->getItemName()), $item_detail);
             * }
             */

            /*
             * if (strlen($row->getItemName()) < 35) {
             * $row->itemName = sprintf('%s <a style="cursor:pointer;color:#337ab7" item-pic="" id="%s" item_name="%s" title="%s" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>', $row->itemName, $row->item, $row->itemName, $row->itemName, $onclick);
             * } else {
             *
             * $row->itemName = sprintf('%s <a style="cursor:pointer;color:#337ab7" item-pic="" id="%s" item_name="%s" title="%s" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>', substr($row->itemName, 0, 30), $row->item, $row->itemName, $row->itemName, $onclick);
             * }
             */

            $row->convertedStandardQuantity = ($row->getConvertedStandardQuantity() > 0 ? number_format($row->getConvertedStandardQuantity(), 0) : $zero);

            $row->standardQoQuantity = ($row->getStandardQoQuantity() > 0 ? number_format($row->getStandardQoQuantity(), 0) : $zero);
            $row->postedStandardQoQuantity = ($row->getPostedStandardQoQuantity() > 0 ? number_format($row->getPostedStandardQoQuantity(), 0) : $zero);

            $row->standardPoQuantity = ($row->getStandardPoQuantity() > 0 ? number_format($row->getStandardPoQuantity(), 0) : $zero);
            $row->postedPoQuantity = ($row->getPostedPoQuantity() > 0 ? number_format($row->getPostedPoQuantity(), 0) : $zero);
            $row->postedStandardPoQuantity = ($row->getPostedStandardPoQuantity() > 0 ? number_format($row->getPostedStandardPoQuantity(), 0) : $zero);

            $row->postedGrQuantity = ($row->getPostedGrQuantity() > 0 ? number_format($row->getPostedGrQuantity(), 0) : $zero);
            $row->postedStandardGrQuantity = ($row->getPostedStandardGrQuantity() > 0 ? number_format($row->getPostedStandardGrQuantity(), 0) : $zero);

            $row->postedStockQrQuantity = ($row->getPostedStockQrQuantity() > 0 ? number_format($row->getPostedStockQrQuantity(), 0) : $zero);
            $row->postedStandardStockQrQuantity = ($row->getPostedStandardStockQrQuantity() > 0 ? number_format($row->getPostedStandardStockQrQuantity(), 0) : $zero);

            $row->postedApQuantity = ($row->getPostedApQuantity() > 0 ? number_format($row->getPostedApQuantity(), 0) : $zero);
            $row->postedStandardApQuantity = ($row->getPostedStandardApQuantity() > 0 ? number_format($row->getPostedStandardApQuantity(), 0) : $zero);

            $decimalNo1 = 2;

            if (in_array($row->getLastCurrency(), $curency)) {
                $decimalNo1 = 2;
            }

            $row->lastStandardUnitPrice = ($row->getLastStandardUnitPrice() !== null ? number_format($row->getLastStandardUnitPrice(), $decimalNo1) : 0);
        }

        return $row;
    }
}

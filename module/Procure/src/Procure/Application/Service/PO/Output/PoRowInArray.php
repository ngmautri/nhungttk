<?php
namespace Procure\Application\Service\PO\Output;

use Zend\Escaper\Escaper;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoRowInArray extends PoRowOutputStrategy
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\PO\Output\PoRowOutputStrategy::createOutput()
     */
    public function createOutput($po)
    {
        if ($po == null)
            return null;

        /**
         *
         * @var \Procure\Domain\PurchaseOrder\GenericPO $po ;
         */

        if (count($po->getDocRows()) == 0)
            return null;

        $output = array();

        $decimalNo = 0;
        $curency = array(
            "USD",
            "THB",
            "EUR"
        );

        if (in_array($po->getCurrencyIso3(), $curency)) {
            $decimalNo = 2;
        }

        foreach ($po->getDocRows() as $row) {

            /**@var \Procure\Domain\PurchaseOrder\PORow $row ;*/

            if ($row == null) {
                continue;
            }

            /**@var \Procure\Application\DTO\Po\PORowForGridDTO $dto ;*/
            $dto = $row->makeDTOForGrid();

            if ($dto == null) {
                continue;
            }

            $escaper = new Escaper();

            $item_detail = sprintf("/inventory/item/show1?token=%s&checksum=%s&entity_id=%s", $dto->itemToken, $dto->itemChecksum, $dto->item);
            $onclick = sprintf("showJqueryDialog('Detail of Item: %s','1600',$(window).height()-50,'%s','j_loaded_data', true);", $escaper->escapeJs($dto->itemName), $item_detail);

            if (strlen($dto->itemName) < 35) {
                $dto->itemName = sprintf('%s <a style="cursor:pointer;color:#337ab7"  item-pic="" id="%s" item_name="%s" title="%s" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>', $dto->itemName, $dto->item, $dto->itemName, $dto->itemName, $onclick);
            } else {

                $dto->itemName = sprintf('%s <a style="cursor:pointer;color:#337ab7"  item-pic="" id="%s" item_name="%s" title="%s" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>', substr($dto->itemName, 0, 30), $dto->item, $dto->itemName, $dto->itemName, $onclick);
            }

            if ($dto->docUnitPrice !== null) {
                $dto->docUnitPrice = number_format($dto->docUnitPrice, $decimalNo);
            }

            if ($dto->postedAPQuantity !== null) {
                $dto->postedAPQuantity = number_format($dto->postedAPQuantity, $decimalNo);
            }
            
            
            if ($dto->draftGrQuantity !== null) {
                $dto->draftGrQuantity = number_format($dto->draftGrQuantity, $decimalNo);
            }
            if ($dto->postedGrQuantity !== null) {
                $dto->postedGrQuantity = number_format($dto->postedGrQuantity, $decimalNo);
            }

            if ($dto->billedAmount !== null) {
                $dto->billedAmount = number_format($dto->billedAmount, $decimalNo);
            }

            if ($dto->netAmount !== null) {
                $dto->netAmount = number_format($dto->netAmount, $decimalNo);
            }

            if ($dto->taxAmount !== null) {
                $dto->taxAmount = number_format($dto->taxAmount, $decimalNo);
            }
            if ($dto->grossAmount !== null) {
                $dto->grossAmount = number_format($dto->grossAmount, $decimalNo);
            }

            if ($dto->convertedStandardQuantity !== null) {
                $dto->convertedStandardQuantity = number_format($dto->convertedStandardQuantity, $decimalNo);
            }

            if ($dto->convertedStandardUnitPrice !== null) {
                $dto->convertedStandardUnitPrice = number_format($dto->convertedStandardUnitPrice, $decimalNo);
            }

            $link = sprintf('<a style="cursor:pointer;color:#337ab7" title="%s" target="_blank" href="/procure/pr/show?token=%s&entity_id=%s&checkum=%s">&nbsp;&nbsp;(i)&nbsp;</a>', 
             $dto->prRowIndentifer, $dto->prToken, $dto->pr, $dto->prChecksum);

            if( $dto->prNumber!==null){
                $dto->prNumber = sprintf('<span style="font-size:8pt; color: graytext">%s %s</span', $dto->prNumber, $link);
            }
            
            $dto->vendorItemName = '<span style="font-size:8pt; color: graytext">' . $dto->vendorItemName . '</span>';
            $dto->vendorItemCode = '<span style="font-size:8pt; color: graytext">' . $dto->vendorItemCode . '</span>';
            

            $output[] = $dto;
        }

        return $output;
    }
}

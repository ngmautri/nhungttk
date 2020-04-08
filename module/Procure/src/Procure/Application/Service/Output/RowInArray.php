<?php
namespace Procure\Application\Service\Output;

use Procure\Domain\GenericDoc;
use Procure\Domain\GenericRow;
use Procure\Domain\RowSnapshot;
use Zend\Escaper\Escaper;
use Procure\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RowInArray extends RowOutputStrategy
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\RowOutputInterface::formatMultiplyRows()
     */
    public function formatMultiplyRows($rows)
    {
        if (count($rows) == 0) {
            return null;
        }

        $output = array();
        foreach ($rows as $row) {
            $output[] = $this->formatRow($row);
        }
        return $output;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\RowOutputInterface::createOutput()
     */
    public function createOutput(GenericDoc $doc)
    {
        if (! $doc instanceof GenericDoc) {
            throw new InvalidArgumentException(sprintf("Invalid input %s", "doc."));
        }
        
        if (count($doc->getDocRows() == null)) {
            return;
        }
        
        $this->formatMultiplyRows($doc->getDocRows());
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\RowOutputStrategy::formatRow()
     */
    public function formatRow(GenericRow $row)
    {
        if ($row instanceof GenericRow) {
            continue;
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

        /**
         *
         * @var RowSnapshot $dto
         */
        $dto = $row->makeSnapshot();

        if ($dto == null) {
            continue;
        }

        $escaper = new Escaper();

        $item_detail = sprintf("/inventory/item/show1?token=%s&checksum=%s&entity_id=%s", $dto->getItemToken(), $dto->getItemChecksum(), $dto->getItem());
        $onclick = sprintf("showJqueryDialog('Detail of Item: %s','1600',$(window).height()-50,'%s','j_loaded_data', true);", $escaper->escapeJs($dto->getItemName()), $item_detail);

        if (strlen($dto->getItemName()) < 35) {
            $dto->itemName = sprintf('%s <a style="cursor:pointer;color:#337ab7"  item-pic="" id="%s" item_name="%s" title="%s" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>', $dto->itemName, $dto->item, $dto->itemName, $dto->itemName, $onclick);
        } else {

            $dto->itemName = sprintf('%s <a style="cursor:pointer;color:#337ab7"  item-pic="" id="%s" item_name="%s" title="%s" href="javascript:;" onclick="%s" >&nbsp;&nbsp;(i)&nbsp;</a>', substr($dto->itemName, 0, 30), $dto->item, $dto->itemName, $dto->itemName, $onclick);
        }

        if ($dto->docUnitPrice !== null) {
            $dto->docUnitPrice = number_format($dto->docUnitPrice, $decimalNo);
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

        $link = sprintf('<a style="cursor:pointer;color:#337ab7" title="%s" target="_blank" href="/procure/pr/show?token=%s&entity_id=%s&checkum=%s">&nbsp;&nbsp;(i)&nbsp;</a>', $dto->prRowIndentifer, $dto->prToken, $dto->pr, $dto->prChecksum);

        if ($dto->prNumber !== null) {
            $dto->prNumber = sprintf('<span style="font-size:8pt; color: graytext">%s %s</span', $dto->prNumber, $link);
        }

        $dto->vendorItemName = '<span style="font-size:8pt; color: graytext">' . $dto->vendorItemName . '</span>';
        $dto->vendorItemCode = '<span style="font-size:8pt; color: graytext">' . $dto->vendorItemCode . '</span>';

       return $dto;
    }

}

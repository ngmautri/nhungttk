<?php
namespace Procure\Application\Service\AP\Output;

use Application\Domain\Util\ExcelColumnMap;
use Procure\Application\Service\Output\AbstractDocSaveAsSpreadsheet;
use Procure\Application\Service\Output\Formatter\AbstractRowFormatter;
use Procure\Domain\GenericDoc;
use Procure\Domain\AccountPayable\APRowSnapshot;

/**
 * Director in builder pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ApSaveAsExcel extends AbstractDocSaveAsSpreadsheet
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Contract\DocSaveAsInterface::saveAs()
     */
    public function saveAs(GenericDoc $doc, AbstractRowFormatter $formatter)
    {
        if ($this->getBuilder() == null) {
            return null;
        }

        if (! $doc instanceof GenericDoc) {
            throw new \InvalidArgumentException(sprintf("Invalid input %s", "doc."));
        }

        if (count($doc->getDocRows()) == null) {
            return;
        }

        // Set Header

        $params = [
            "docNumber" => $doc->getSysNumber()
        ];
        $this->getBuilder()->buildHeader($params);

        $objPHPExcel = $this->getBuilder()->getPhpSpreadsheet();

        $header = 7;
        $i = 0;

        $cols = ExcelColumnMap::COLS;

        $headerValues = array(
            "#",
            "Remark",
            "GL",
            "CC",
            "RowID",
            "Row#",
            "SKU",
            "ItemName",
            "Vendor ItemName",
            "Vendor ItemName",
            "Unit",
            "Qty",
            "UP",
            "Net Amt",
            "CUrr",
            "remark",
            "PR row",
            "PR",
            "ItemId",
            "docNumber"
        );

        $n = 0;
        foreach ($headerValues as $v) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cols[$n] . $header, $v);
            $n ++;
        }

        foreach ($doc->getDocRows() as $r) {

            /**
             *
             * @var APRowSnapshot $row ;
             */
            $row = $formatter->format($r->makeSnapshot());

            $i ++;
            $l = $header + $i;

            $columnValues = array(
                $i,
                $row->faRemarks,
                $row->glAccount,
                $row->costCenter,
                $row->rowIdentifer,
                $row->rowNumber,
                $row->itemSKU,
                $row->itemName,
                $row->vendorItemName,
                $row->vendorItemCode,
                $row->docUnit,
                $row->quantity,
                $row->docUnitPrice,
                $row->netAmount,
                $row->docCurrencyISO,
                $row->remarks,
                $row->prRowIndentifer,
                $row->prNumber,
                $row->item,
                $row->docNumber
            );

            $n = 0;
            foreach ($columnValues as $v) {

                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cols[$n] . $l, $v);
                $n ++;
            }
        }

        $params = [
            "docNumber" => $doc->getSysNumber()
        ];
        // created footer and export
        $this->getBuilder()->buildFooter($params);
    }
}

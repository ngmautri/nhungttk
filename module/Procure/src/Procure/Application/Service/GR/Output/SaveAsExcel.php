<?php
namespace Procure\Application\Service\GR\Output;

use Application\Domain\Util\ExcelColumnMap;
use Procure\Application\Service\Output\AbstractDocSaveAsSpreadsheet;
use Procure\Application\Service\Output\Formatter\AbstractRowFormatter;
use Procure\Domain\GenericDoc;
use Procure\Domain\GoodsReceipt\GRRowSnapshot;

/**
 * Director in builder pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SaveAsExcel extends AbstractDocSaveAsSpreadsheet
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

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("E2", "Goods Receipt");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue("A6", $doc->getVendorName());

        $cols = ExcelColumnMap::COLS;

        $headerValues = array(
            "#",
            "Vendor",
            "PO#",
            "Item",
            "SKU",
            "Item Vendor Name",
            "Item Vendor code",
            "Unit",
            "Qty",
            "UP",
            "Net Amt",
            "CUrr",
            "PR",
            "PR"
        );

        $n = 0;
        foreach ($headerValues as $v) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cols[$n] . $header, $v);
            $n ++;
        }

        foreach ($doc->getDocRows() as $r) {

            /**
             *
             * @var GRRowSnapshot $row ;
             */
            $row = $formatter->format($r->makeSnapshot());

            $i ++;
            $l = $header + $i;

            $columnValues = array(
                $i,
                $row->getVendorName(),
                $row->getDocNumber(),
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
                $row->prNumber
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

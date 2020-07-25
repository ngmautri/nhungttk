<?php
namespace Inventory\Application\Reporting\Item\Export;

use Application\Domain\Util\ExcelColumnMap;
use Inventory\Application\Export\Item\AbstractSaveAsSpreadsheet;
use Procure\Application\Service\Output\Formatter\AbstractRowFormatter;
use Procure\Domain\AccountPayable\APRowSnapshot;

/**
 * Director in builder pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SaveAsExcel extends AbstractSaveAsSpreadsheet
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Application\Export\Item\Contracts\SaveAsInterface::saveAs()
     */
    public function saveAs($rows, AbstractRowFormatter $formatter)
    {
        if ($this->getBuilder() == null) {
            return null;
        }

        if (count($rows) == 0) {
            return null;
        }

        // created header
        $params = [];

        $this->getBuilder()->buildHeader($params);
        $objPHPExcel = $this->getBuilder()->getPhpSpreadsheet();

        $header = 3;
        $i = 0;

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

        foreach ($rows as $row) {

            $formatter->format($row);

            /**
             *
             * @var APRowSnapshot $row ;
             */

            $i ++;
            $l = $header + $i;

            $columnValues = array(
                $i,
                $row->vendorName,
                $row->docNumber,
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

        // created footer and export
        $params = [];
        $this->getBuilder()->buildFooter($params);
    }
}

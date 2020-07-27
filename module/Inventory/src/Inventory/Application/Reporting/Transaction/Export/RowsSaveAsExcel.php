<?php
namespace Inventory\Application\Reporting\Transaction\Export;

use Application\Domain\Util\ExcelColumnMap;
use Inventory\Application\Export\Transaction\AbstractRowsSaveAsSpreadsheet;
use Inventory\Application\Export\Transaction\Formatter\AbstractRowFormatter;
use Inventory\Domain\Transaction\TrxRowSnapshot;

/**
 * Director in builder pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RowsSaveAsExcel extends AbstractRowsSaveAsSpreadsheet
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

        $header = 7;
        $i = 0;

        $cols = ExcelColumnMap::COLS;

        $headerValues = array(
            "#",
            "Date",
            "SKU",
            "ItemName",
            "Flow"
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
             * @var TrxRowSnapshot $row ;
             */

            $i ++;
            $l = $header + $i;

            $columnValues = array(
                $i,
                $row->getTrxDate(),
                $row->itemSKU,
                $row->itemName,
                $row->flow
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

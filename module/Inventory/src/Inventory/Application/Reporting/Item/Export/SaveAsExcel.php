<?php
namespace Inventory\Application\Reporting\Item\Export;

use Application\Domain\Util\ExcelColumnMap;
use Inventory\Application\Export\Item\AbstractSaveAsSpreadsheet;
use Inventory\Application\Export\Item\Formatter\AbstractFormatter;
use Inventory\Domain\Item\ItemSnapshot;

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
    public function saveAs($rows, AbstractFormatter $formatter)
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
            "ID",
            "SKU",
            "Ref.",
            "ItemName",
            "ItemName1",
            "Mfg Code",
            "Mfg Model",
            "Mfg S/N",
            "HS Code"
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
             * @var ItemSnapshot $row ;
             */

            $i ++;
            $l = $header + $i;

            $columnValues = array(
                $i,
                $row->id,
                $row->itemSku,
                $row->sysNumber,
                $row->itemName,
                $row->itemNameForeign,
                $row->manufacturerCode,
                $row->manufacturerModel,
                $row->manufacturerSerial,
                $row->hsCode
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

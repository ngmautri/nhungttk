<?php
namespace Inventory\Application\Reporting\Item\Export;

use Application\Domain\Util\ExcelColumnMap;
use Inventory\Application\DTO\Item\Report\ItemInOutOnhandDTO;
use Inventory\Application\Export\Item\AbstractSaveAsSpreadsheet;
use Inventory\Application\Export\Item\Formatter\AbstractFormatter;

/**
 * Director in builder pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class InOutOnhandSaveAsExcel extends AbstractSaveAsSpreadsheet
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
            "SysNo",

            "SKU",

            "ItemName",
            "Mfg Model",
            "Mfg S/N",

            "Begin Qty",
            "GR Qty",
            "GI Qty",
            "End Qty",
            "Begin Value",
            "GR Value",
            "GI Value",
            "End Value",

            "Remarks"
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
             * @var ItemInOutOnhandDTO $row ;
             */

            $i ++;
            $l = $header + $i;

            $columnValues = array(
                $i,
                $row->id,
                $row->sysNumber,
                $row->itemSku,
                $row->itemName,
                $row->manufacturerModel,
                $row->manufacturerCode,
                $row->beginQty,
                $row->grQty,
                $row->giQty,
                $row->endQty,
                $row->beginValue,
                $row->grValue,
                $row->giValue,
                $row->endValue
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

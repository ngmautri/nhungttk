<?php
namespace Procure\Application\Reporting\PR\Output;

use Application\Domain\Util\ExcelColumnMap;
use Procure\Application\Service\Output\AbstractRowsSaveAsSpreadsheet;
use Procure\Application\Service\Output\Formatter\AbstractRowFormatter;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;

/**
 * Director in builder pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SaveAsExcel extends AbstractRowsSaveAsSpreadsheet
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Contract\RowsSaveAsInterface::saveAs()
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
            "SKU",
            "Sys No.",
            "Item",
            "Item Vendor Name",
            "Item Vendor code",
            "Item code",
            "PR ",
            "PR Date",
            "Row#",
            "Unit",
            "PR Qty",
            "QO",
            "Posted QO",            
            "PO",
            "Posted PO",
            "AP",
            "Posted AP",
            "last Vendor",
            "last UP"
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
             * @var PRRowSnapshot $row ;
             */

            $i ++;
            $l = $header + $i;

            $columnValues = array(
                $i,
                $row->itemSKU,
                $row->itemSysNumber,
                $row->itemName,
                $row->vendorItemName,
                $row->vendorItemCode,
                $row->itemManufacturerCode,
                $row->docNumber,
                $row->docDate,
                $row->rowIdentifer,
                $row->docUnit,
                $row->docQuantity,
                $row->draftQoQuantity,
                $row->postedQoQuantity,                
                $row->draftPoQuantity,
                $row->postedPoQuantity,
                $row->draftApQuantity,
                $row->postedApQuantity,
                $row->lastVendorName,
                $row->lastUnitPrice,
                $row->lastCurrency
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

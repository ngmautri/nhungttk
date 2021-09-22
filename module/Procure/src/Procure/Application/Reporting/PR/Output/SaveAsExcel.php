<?php
namespace Procure\Application\Reporting\PR\Output;

use Application\Domain\Util\ExcelColumnMap;
use Procure\Application\Service\Output\AbstractRowsSaveAsSpreadsheet;
use Procure\Application\Service\Output\Formatter\AbstractRowFormatter;
use Procure\Domain\PurchaseRequest\PRRow;
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
            "Status",
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

            /**
             *
             * @var PRRow $row ;
             */
            $row->updateRowStatus(); // important
            $formatter->format($row->makeSnapshot());

            /**
             *
             * @var PRRowSnapshot $row ;
             */

            $i ++;
            $l = $header + $i;

            $columnValues = array(
                $i,
                $row->getTransactionStatus(),
                $row->getItemSKU(),
                $row->getItemSysNumber(),
                $row->getItemName(),
                $row->getVendorItemName(),
                $row->getVendorItemCode(),
                $row->getItemManufacturerCode(),
                $row->getDocNumber(),
                $row->getDocDate(),
                $row->getRowIdentifer(),
                $row->getDocUnit(),
                $row->getDocQuantity(),
                $row->getConvertedStandardQuantity(),
                $row->getPostedStandardQoQuantity(),
                $row->getPostedStandardPoQuantity(),
                $row->getPostedStandardGrQuantity(),
                $row->getPostedStandardApQuantity(),
                $row->getLastVendorName(),
                $row->getLastStandardUnitPrice(),
                $row->getLastCurrency()
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

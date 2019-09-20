<?php
namespace Procure\Application\Reporting\PO\Output;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Procure\Domain\PurchaseOrder\PORowDetailsSnapshot;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoRowStatusInExcel extends PoRowStatusOutputStrategy
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Reporting\PR\Output\PrRowStatusOutputStrategy::createOutput()
     */
    public function createOutput($result)
    {
        if (count($result) == 0)
            return null;

        // Create new PHPExcel object
        $objPHPExcel = new Spreadsheet();

        // $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        /*
         * $drawing->setName('Logo');
         * $drawing->setDescription('Logo');
         * $drawing->setPath(ROOT . '/public/images/mascot.gif');
         * $drawing->setHeight(120);
         */

        // $drawing->setWorksheet($objPHPExcel->getActiveSheet());

        // Set document properties
        $objPHPExcel->getProperties()
            ->setCreator("Nguyen Mau Tri")
            ->setLastModifiedBy("Nguyen Mau Tri")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");

        $header = 3;
        $i = 0;

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $header, "#");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $header, "Vendor");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $header, "PO#");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $header, "Item");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $header, "SKU");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $header, "Item Vendor Name");

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $header, "Item Vendor code");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $header, "Unit");

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $header, "Qty");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $header, "UP");

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $header, "Net Amt");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $header, "CUrr");

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $header, "Draft GR");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $header, "Posted GR");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $header, "Draft AP#");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $header, "Posted AP");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $header, "Billed Amt");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $header, "Open Amt");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S' . $header, "Remarks");
        
        foreach ($result as $a) {

            /**@var PORowDetailsSnapshot $a ;*/
            $i ++;
            $l = $header + $i;

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $l, $i);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $l, $a->vendorName);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $l, $a->poNumber);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $l, $a->itemName);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $l, $a->itemSKU);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $l, $a->vendorItemName);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $l, $a->vendorItemCode);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $l, $a->docUnit);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $l, $a->quantity);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $l, $a->docUnitPrice);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $l, $a->netAmount);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $l, $a->docCurrencyISO);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $l, $a->draftGrQuantity);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $l, $a->postedGrQuantity);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $l, $a->draftAPQuantity);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $l, $a->postedAPQuantity);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $l, $a->billedAmount);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $l, $a->openAPAmount);
        }

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setAutoFilter("A3:R3");

        // Redirect output to a client's web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . 'all' . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        // header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
        $objWriter->save('php://output');
        exit();
    }
}

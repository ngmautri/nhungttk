<?php
namespace Procure\Application\DTO\Po\Output;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Zend\Escaper\Escaper;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PORowInExcel extends PORowOutputStrategy
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\DTO\Ap\Output\APDocRowOutputStrategy::createOutput()
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
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $header, "PR Number");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $header, "PR Date");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $header, "SKU");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $header, "Item Name");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $header, "PR Qty");

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $header, "PO Qty");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $header, "Posted PO Qty");

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $header, "GR Qty");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $header, "Posted GR Qty");

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $header, "Stock GR Qty");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $header, "Stock Posted GR Qty");

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $header, "AP Qty");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $header, "Posted AP Qty");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $header, "PR#");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $header, "Row ID");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $header, "Row name");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $header, "Row Code");

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S' . $header, "Last Vendor");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('T' . $header, "Last UP");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('U' . $header, "Curr");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('V' . $header, "ItemCode");

        foreach ($result as $r) {

            /**@var \Application\Entity\NmtProcurePrRow $a ;*/
            $a = $r[0];

            if ($a->getPr() == null || $a->getItem() == null) {
                continue;
            }

            $i ++;
            $l = $header + $i;

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $l, $i);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $l, $a->getPr()
                ->getPrName());
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $l, $a->getPr()
                ->getSubmittedOn());

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $l, $a->getItem()
                ->getItemSku());
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $l, $a->getItem()
                ->getItemName());

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $l, $a->getQuantity());

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $l, $r['po_qty']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $l, $r['posted_po_qty']);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $l, $r['gr_qty']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $l, $r['posted_gr_qty']);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $l, $r['stock_gr_qty']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $l, $r['posted_stock_gr_qty']);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $l, $r['ap_qty']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $l, $r['posted_ap_qty']);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $l, $a->getPr()
                ->getPrAutoNumber());
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $l, $a->getRowIdentifer());

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $l, $a->getRowName());
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $l, $a->getRowCode());

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S' . $l, $r['vendor_name']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('T' . $l, $r['unit_price']);

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('U' . $l, $r['currency_iso3']);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('V' . $l, $a->getItem()
                ->getSysNumber());
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

    public function createRowOutputFromSnapshot($result)
    {}
}

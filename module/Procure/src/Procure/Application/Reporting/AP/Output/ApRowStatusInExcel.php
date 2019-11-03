<?php
namespace Procure\Application\Reporting\AP\Output;

use Application\Domain\Util\ExcelColumnMap;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Procure\Domain\APInvoice\APDocRowDetailsSnapshot;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ApRowStatusInExcel extends ApRowStatusOutputStrategy
{

    protected function createHeader(Spreadsheet $objPHPExcel)
    {

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
    }

    protected function createFooter(Spreadsheet $objPHPExcel)
    {
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setAutoFilter("A3:U3");

        // Redirect output to a client's web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . 'ap-report' . '.xlsx"');
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

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Reporting\AP\Output\ApRowStatusOutputStrategy::createOutput()
     */
    public function createOutput($result)
    {
        if (count($result) == 0)
            return null;

        // Create new PHPExcel object
        $objPHPExcel = new Spreadsheet();

        // created header
        $this->createHeader($objPHPExcel);

        $header = 3;
        $i = 0;

        $cols = ExcelColumnMap::COLS;

        $headerValues = array(
            "#",
            "Vendor",
            "PO#",
            "Cur",
            "SKU",
            "Item  Name",            
            "Unit",
            "Qty",
            "Net Amt",
            "FX",
            "PR",
            "PO",
            "Remarks",
        );

        $n = 0;
        foreach ($headerValues as $v) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cols[$n] . $header, $v);
            $n ++;
        }

        foreach ($result as $a) {

            /**@var APDocRowDetailsSnapshot $a ;*/
            $i ++;
            $l = $header + $i;

            $columnValues = array(
                $i,
                $a->vendorName,
                $a->sapNumber,
                $a->docCurrencyISO,
                $a->itemSKU,
                $a->itemName,
                $a->docQuantity,
                $a->docUnitPrice,
                $a->netAmount,
                $a->exchangeRate,
                $a->prNumber,
                $a->poNumber,
                $a->remarks,
                $a->itemSKU1,
                $a->itemSKU2,
            );

            $n = 0;
            foreach ($columnValues as $v) {

                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cols[$n] . $l, $v);
                $n ++;
            }
        }

        // created footer and export
        $this->createFooter($objPHPExcel);

    }
  
}

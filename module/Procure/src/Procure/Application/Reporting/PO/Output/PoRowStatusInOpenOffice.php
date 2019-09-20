<?php
namespace Procure\Application\Reporting\PO\Output;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoRowStatusInOpenOffice extends PoRowStatusInExcel
{

   /**
    * 
    * @param \PhpOffice\PhpSpreadsheet\Spreadsheet $objPHPExcel
    */
    protected function createHeader(\PhpOffice\PhpSpreadsheet\Spreadsheet $objPHPExcel)
    {
        // TODO Auto-generated method stub
        
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
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Reporting\PO\Output\PoRowStatusInExcel::createHeader()
     */
    protected function createFooter(\PhpOffice\PhpSpreadsheet\Spreadsheet $objPHPExcel)
    {
        // TODO Auto-generated method stub
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $objPHPExcel->getActiveSheet()->setAutoFilter("A3:U3");

        // Redirect output to a client's web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . 'all' . '.ods"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        // header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Ods');
        $objWriter->save('php://output');
        exit();
    }
}

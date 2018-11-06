<?php
namespace Procure\Model\Qo;

use Application\Entity\NmtProcureQo;
use Doctrine\ORM\EntityManager;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ExcelStrategy extends DownloadStrategyAbstract
{   

    /**
     * {@inheritDoc}
     * @see \Procure\Model\Qo\DownloadStrategyAbstract::doDownloadRows()
     */
    public function doDownloadRows($qo, $rows)
    {
        // TODO Auto-generated method stub
        
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Model\Pr\DownloadStrategyAbstract::doDownload()
     */
    public function doDownload($entity)
    {
        if (! $entity instanceof NmtProcureQo) {
            throw new \Exception();
        }

        $criteria = array(
            'isActive' => 1,
            'qo' => $entity
        );

        /**@var \Application\Entity\NmtProcureQo $entity*/
        $rows = $this->doctrineEM->getRepository('Application\Entity\NmtProcureQoRow')->findBy($criteria);

        if (count($rows) == null) {
            throw new \Exception();
        }

        // Create new PHPExcel object
        $objPHPExcel = new Spreadsheet();

      /*   $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Logo');
        $drawing->setPath(ROOT . '/public/images/mascot.gif');
        $drawing->setHeight(120);
 */
       //$drawing->setWorksheet($objPHPExcel->getActiveSheet());

        // Set document properties
        $objPHPExcel->getProperties()
            ->setCreator("Nguyen Mau Tri")
            ->setLastModifiedBy("Nguyen Mau Tri")
            ->setTitle("Office 2007 XLSX Test Document")
            ->setSubject("Office 2007 XLSX Test Document")
            ->setDescription("Test document for Office 2007 XLSX, generated using PHP classes.")
            ->setKeywords("office 2007 openxml php")
            ->setCategory("Test result file");

        // Add some data
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', $entity->getQuotationNo());

        // Add some data
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', $entity->getCreatedOn());

        $header = 3;
        $i = 0;

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $header, "#");

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $header, "Item");

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $header, "SKU");

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $header, "Quantity");

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $header, "Received");

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $header, "Balance");

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $header, "Buying");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $header, "Item.No.");

        foreach ($rows as $r) {

            /**@var \Application\Entity\NmtProcureQoRow $a ;*/
            $a = $r;

            $i ++;
            $l = $header + $i;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $l, $i);
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $l, $a->getItem()
                ->getItemName());

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $l, $a->getQuantity());

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $l, $a->getUnitPrice());

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $l, $a->getUnit());

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $l, $a->getUnit());

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $l, $a->getItem()
                ->getItemSku());
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $l, $a->getItem()
                ->getSysNumber());
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle($entity->getSysNumber());

        $objPHPExcel->getActiveSheet()->setAutoFilter("A3:H3");

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client's web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $entity->getSysNumber() . '.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
        header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header('Pragma: public'); // HTTP/1.0

        $objWriter = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, 'Xlsx');
        $objWriter->save('php://output');
        exit();
    }
    
  
}
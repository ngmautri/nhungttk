<?php
namespace Procure\Application\Service\PO\Output;

use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoRowInExcel extends PoRowOutputStrategy
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\PO\Output\PoRowOutputStrategy::createOutput()
     */
    public function createOutput($po)
    {
        if ($po == null)
            return null;

        /**
         *
         * @var \Procure\Domain\PurchaseOrder\GenericPO $po ;
         */

        if (count($po->getDocRows()) == 0) {
            return null;
        }

        // Create new PHPExcel object
        $objPHPExcel = new Spreadsheet();

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
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', $po->getInvoiceNo()); // Add some data
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C1', $po->getInvoiceDate());

        $header = 3;
        $i = 0;

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', "Contract/PO:" . $po->getSysNumber());

        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $header, "FA Remarks");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $header, "#");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $header, "SKU");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $header, "Item");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $header, "Unit");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $header, "Quantity");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $header, "Unit Price");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $header, "Net Amount");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $header, "Tax Rate");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $header, "Tax Amount");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $header, "Gross Amount");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $header, "PR Number");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $header, "PR Date");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $header, "Requested Q/ty");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $header, "Requested Name");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $header, "RowNo.");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $header, "Remarks");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $header, "Ref.No.");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S' . $header, "Item.No.");
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('T' . $header, "Po.Item Name");

        foreach ($po->getDocRows() as $row) {

            /**@var \Procure\Domain\PurchaseOrder\PORow $row ;*/

            if ($row == null) {
                continue;
            }

            $i ++;
            $l = $header + $i;
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A' . $l, $row->getFaRemarks());
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B' . $l, $i);

            if ($row->getItem() !== null) {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $l, $row->g()
                    ->getItemSku());
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $l, $row->getItem()
                    ->getItemName());
            } else {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('C' . $l, "NA");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('D' . $l, "NA");
            }
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('E' . $l, $row->getUnit());
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('F' . $l, $row->getQuantity());
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('G' . $l, $row->getUnitPrice());
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('H' . $l, $row->getNetAmount());
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('I' . $l, $row->getTaxRate());
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('J' . $l, $row->getTaxAmount());
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('K' . $l, $row->getGrossAmount());

            if ($row->getPrRow() !== null) {

                if ($row->getPrRow()->getPr() !== null) {
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $l, $row->getPrRow()
                        ->getPr()
                        ->getPrNumber());
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $l, $row->getPrRow()
                        ->getPr()
                        ->getSubmittedOn());
                }
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $l, $row->getPrRow()
                    ->getQuantity());
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $l, $row->getPrRow()
                    ->getRowName());
            } else {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('L' . $l, "NA");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('M' . $l, "NA");
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('N' . $l, 0);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue('O' . $l, "");
            }

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('P' . $l, $row->getRowNumber());
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('Q' . $l, $row->getRemarks());

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $l, "-");

            if ($row->getPrRow() !== null) {

                if ($row->getPrRow()->getPr() !== null) {
                    $objPHPExcel->setActiveSheetIndex(0)->setCellValue('R' . $l, $row->getPrRow()
                        ->getPr()
                        ->getPrNumber());
                }
            }

            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('S' . $l, $row->getItem()
                ->getSysNumber());
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue('T' . $l, $row->getVendorItemCode());
        }

        // Rename worksheet
        $objPHPExcel->getActiveSheet()->setTitle("Contract-PO");

        $objPHPExcel->getActiveSheet()->setAutoFilter("A" . $header . ":T" . $header);

        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="invoice' . $po->getId() . '.xlsx"');
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

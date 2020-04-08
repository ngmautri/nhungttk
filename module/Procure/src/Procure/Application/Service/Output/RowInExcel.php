<?php
namespace Procure\Application\Service\Output;

use Application\Domain\Util\ExcelColumnMap;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use Procure\Domain\GenericDoc;
use Procure\Domain\GenericRow;
use Procure\Domain\Exception\InvalidArgumentException;

/**
 * PR Row Service.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RowInExcel extends RowOutputStrategy
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\RowOutputInterface::formatMultiplyRows()
     */
    public function formatMultiplyRows($rows)
    {
        if (count($rows) == 0)
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
            "Item Name",
            "Unit",
            "Qty",
            "Unit Price",
            "Net Amt",
            "Tax",
            "Gross"
        );

        $n = 0;
        foreach ($headerValues as $v) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cols[$n] . $header, $v);
            $n ++;
        }

        foreach ($rows as $row) {

            $i ++;
            $l = $header + $i;

            $columnValues = $this->formatRow($row);

            $n = 0;
            foreach ($columnValues as $v) {
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cols[$n] . $l, $v);
                $n ++;
            }
        }

        // created footer and export
        $this->createFooter($objPHPExcel);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\RowOutputStrategy::formatRowOutput()
     */
    public function formatRow(GenericRow $row)
    {
        if ($row instanceof GenericRow) {
            continue;
        }

        $decimalNo = 0;
        $curency = array(
            "USD",
            "THB",
            "EUR"
        );

        if (in_array($row->getDocCurrencyISO(), $curency)) {
            $decimalNo = 2;
        }

        $docUnitPrice = $row->getDocUnitPrice();
        if ($docUnitPrice !== null) {
            $docUnitPrice = number_format($docUnitPrice, $decimalNo);
        }

        $netAmount = $row->getNetAmount();
        if ($netAmount !== null) {
            $netAmount = number_format($netAmount, $decimalNo);
        }

        $taxAmount = $row->getTaxAmount();
        if ($taxAmount !== null) {
            $taxAmount = number_format($taxAmount, $decimalNo);
        }

        $grossAmount = $row();
        if ($grossAmount !== null) {
            $grossAmount = number_format($grossAmount, $decimalNo);
        }

        $columnValues = [
            $row->getItem(),
            $row->getDocUnit(),
            $row->getDocQuantity(),
            $docUnitPrice,
            $netAmount,
            $taxAmount,
            $grossAmount
        ];

        return $columnValues;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\RowOutputStrategy::createOutput()
     */
    public function createOutput(GenericDoc $doc)
    {
        if (! $doc instanceof GenericDoc) {
            throw new InvalidArgumentException(sprintf("Invalid input %s", "doc."));
        }

        if (count($doc->getDocRows() == null)) {
            return;
        }

        $this->formatMultiplyRows($doc->getDocRows());
    }

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
     
   
}
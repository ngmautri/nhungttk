<?php
namespace Procure\Application\Reporting\GR\Output\Header;

use Application\Domain\Util\ExcelColumnMap;
use Procure\Application\Service\Output\Header\AbstractHeaderFormatter;
use Procure\Application\Service\Output\Header\AbstractHeaderSaveAsSpreadsheet;
use Procure\Domain\DocSnapshot;

/**
 * Director in builder pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class HeaderSaveAsExcel extends AbstractHeaderSaveAsSpreadsheet
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Header\HeaderSaveAsInterface::saveMultiplyHeaderAs()
     */
    public function saveMultiplyHeaderAs($headers, AbstractHeaderFormatter $formatter)
    {
        if ($this->getBuilder() == null) {
            return null;
        }

        if (count($headers) == 0) {
            return null;
        }

        // created header
        $params = [];

        $this->getBuilder()->buildHeader($params);
        $objPHPExcel = $this->getBuilder()->getPhpSpreadsheet();

        $header_row = 7;
        $i = 0;

        $cols = ExcelColumnMap::COLS;

        $headerValues = array(
            "#",
            "Vendor",
            "Quotation#",
            "Date",
            "Gross",
            "Cur"
        );

        $n = 0;
        foreach ($headerValues as $v) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cols[$n] . $header_row, $v);
            $n ++;
        }

        foreach ($headers as $header) {

            $formatter->format($header);

            /**
             *
             * @var DocSnapshot $header ;
             */

            $i ++;
            $l = $header_row + $i;

            $columnValues = array(
                $i,
                $header->vendorName,
                $header->docNumber,
                $header->docDate,
                $header->getDocCurrencyISO(),
                $header->getGrossAmount()
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
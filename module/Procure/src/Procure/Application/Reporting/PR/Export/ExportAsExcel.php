<?php
namespace Procure\Application\Reporting\PR\Export;

use Application\Domain\Util\ExcelColumnMap;
use Application\Domain\Util\Collection\Contracts\ElementFormatterInterface;
use Application\Domain\Util\Collection\Contracts\FilterInterface;
use Application\Domain\Util\Collection\Export\AbstractExportAsSpreadsheet;
use Application\Domain\Util\Collection\Filter\DefaultFilter;
use Application\Domain\Util\Collection\Formatter\NullFormatter;
use Procure\Infrastructure\Persistence\Reporting\DTO\PoApDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ExportAsExcel extends AbstractExportAsSpreadsheet
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Collection\Contracts\ExportInterface::execute()
     */
    public function execute(\Traversable $collection, FilterInterface $filter = null, ElementFormatterInterface $formatter = null)
    {
        if ($this->getBuilder() == null) {
            return null;
        }

        if ($collection->isEmpty()) {
            return "Nothing found!";
        }

        if ($formatter == null) {
            $formatter = new NullFormatter();
        }

        if ($filter == null) {
            $filter = new DefaultFilter();
        }

        // created header
        $params = [];

        $this->getBuilder()->buildHeader($params);
        $objPHPExcel = $this->getBuilder()->getPhpSpreadsheet();

        $header = 7;
        $i = 0;

        $cols = ExcelColumnMap::COLS;

        $headerValues = [
            "#",
            "DocType",
            "VendorId",
            "Vendor",
            "DocNo",
            "DocSys",
            "DocDate",
            "WH",
            "Item",
            "Item",
            "RowiD",
            "Row#",
            "DocQty",
            "CF",
            "Standard Qty",
            "UP",
            "PrId"
        ];

        $n = 0;
        foreach ($headerValues as $v) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cols[$n] . $header, $v);
            $n ++;
        }

        foreach ($collection as $row) {

            $formatter->format($row);

            $i ++;
            $l = $header + $i;

            /**
             *
             * @var PoApDTO $columnValues ;
             */

            // $columnValues->rowIdentifer

            $columnValues = [
                $i,
                $row['docTypeName'],
                $row['vendorId'],
                $row['vendorName'],
                $row['docNumber'],
                $row['docSysNumber'],
                $row['docDate'],
                $row['warehouseName'],
                $row['itemName'],
                $row['itemSysNumber'],
                $row['rowId'],
                $row['rowIdentifer'],
                $row['rowDocQuantity'],
                $row['rowStandardConvertFactor'],
                $row['convertedStandardQuantity'],
                $row['convertedStandardUnitPrice'],
                $row['prRowId']
            ];

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


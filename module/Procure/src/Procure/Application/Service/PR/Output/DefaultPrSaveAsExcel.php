<?php
namespace Procure\Application\Service\PR\Output;

use Application\Domain\Util\ExcelColumnMap;
use Procure\Application\Service\Output\AbstractProcureDocSaveAsSpreadsheet;
use Procure\Application\Service\Output\Formatter\AbstractRowFormatter;
use Procure\Domain\GenericDoc;
use Procure\Domain\PurchaseRequest\PRRow;
use Procure\Domain\PurchaseRequest\PRRowSnapshot;

/**
 * Director in builder pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultPrSaveAsExcel extends AbstractProcureDocSaveAsSpreadsheet
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Contract\ProcureDocSaveAsInterface::saveAs()
     */
    public function saveAs(GenericDoc $doc, AbstractRowFormatter $formatter, $offset = null, $limit = null)
    {
        if ($this->getBuilder() == null) {
            return null;
        }

        if (! $doc instanceof GenericDoc) {
            throw new \InvalidArgumentException(sprintf("Invalid input %s", "doc."));
        }

        $doc->refreshDoc(); // important

        if ($doc->getRowCollection()->count() == 0) {
            return;
        }

        // Set Header

        $params = [
            "docNumber" => $doc->getSysNumber()
        ];
        $this->getBuilder()->buildHeader($params);

        $objPHPExcel = $this->getBuilder()->getPhpSpreadsheet();

        $objPHPExcel->setActiveSheetIndex(0)
            ->getRowDimension(1)
            ->setRowHeight(80);

        $objPHPExcel->setActiveSheetIndex(0)
            ->getColumnDimension('B')
            ->setWidth(30);

        $header = 7;
        $i = 0;

        $cols = ExcelColumnMap::COLS;

        $headerValues = array(
            "#",
            "Picture",
            "Vendor",
            "PO#",
            "Item",
            "SKU",
            "Item Vendor Name",
            "Item Vendor code",
            "Unit",
            "Qty",
            "UP",
            "Net Amt",
            "CUrr",
            "PR",
            "PR"
        );

        $n = 0;
        foreach ($headerValues as $v) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cols[$n] . $header, $v);
            $n ++;
        }

        foreach ($doc->getRowCollection() as $r) {

            /**
             *
             * @var PRRow $r ;
             * @var PRRowSnapshot $row ;
             */

            $r->updateRowStatus(); // important
            $row = $formatter->format($r->makeSnapshot());

            $i ++;
            $l = $header + $i;

            $columnValues = array(
                $i,
                $row->getTransactionStatus(),
                $row->getVendorName(),
                $row->getDocNumber(),
                $row->itemSKU,
                $row->itemName,
                $row->vendorItemName,
                $row->vendorItemCode,
                $row->docUnit,
                $row->quantity,
                $row->docUnitPrice,
                $row->netAmount,
                $row->docCurrencyISO,
                $row->remarks,
                $row->prRowIndentifer,
                $row->prNumber
            );

            $n = 0;
            foreach ($columnValues as $v) {

                $objPHPExcel->setActiveSheetIndex(0)
                    ->getRowDimension($l)
                    ->setRowHeight(100);
                $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cols[$n] . $l, $v);

                $n ++;
            }
        }

        $params = [
            "docNumber" => $doc->getSysNumber()
        ];
        // created footer and export
        $this->getBuilder()->buildFooter($params);
    }
}
<?php
namespace Procure\Application\Service\PO\Output;

use Application\Domain\Util\ExcelColumnMap;
use Procure\Application\Service\Output\AbstractDocSaveAsSpreadsheet;
use Procure\Application\Service\Output\Formatter\AbstractRowFormatter;
use Procure\Domain\GenericDoc;
use Procure\Domain\PurchaseOrder\PORowSnapshot;

/**
 * Director
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PoSaveAsExcel extends AbstractDocSaveAsSpreadsheet
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\Contract\DocSaveAsInterface::saveAs()
     */
    public function saveAs(GenericDoc $doc, AbstractRowFormatter $formatter)
    {
        if ($this->getBuilder() == null) {
            return null;
        }

        if (! $doc instanceof GenericDoc) {
            throw new \InvalidArgumentException(sprintf("Invalid input %s", "doc."));
        }

        if (count($doc->getDocRows()) == null) {
            return;
        }

        // Set Header

        $params = [
            "docNumber" => $doc->getSysNumber()
        ];
        $this->getBuilder()->buildHeader($params);

        $objPHPExcel = $this->getBuilder()->getPhpSpreadsheet();

        $header = 7;
        $i = 0;

        $cols = ExcelColumnMap::COLS;

        $headerValues = array(
            "#",
            "Row#",
            "Vendor",
            "PO#",
            "Item",
            "SKU",
            "SysNo",
            "Item Vendor Name",
            "Item Vendor code",
            "Unit",
            "Qty",
            "UP",
            "Net Amt",
            "CUrr",
            "Draft GR",
            "Posted GR",
            "Draft AP#",
            "Posted AP",
            "Billed Amt",
            "Open Amt",
            "PR",
            "PR"
        );

        $n = 0;
        foreach ($headerValues as $v) {
            $objPHPExcel->setActiveSheetIndex(0)->setCellValue($cols[$n] . $header, $v);
            $n ++;
        }

        foreach ($doc->getDocRows() as $r) {

            /**
             *
             * @var PORowSnapshot $row ;
             */
            $row = $formatter->format($r->makeSnapshot());

            $i ++;
            $l = $header + $i;

            $columnValues = array(
                $i,
                $row->getRowNumber(),
                $row->getVendorName(),
                $row->getDocNumber(),
                $row->itemSKU,
                $row->getItemSysNumber(),
                $row->itemName,
                $row->itemManufacturerModel,
                $row->vendorItemName,
                $row->vendorItemCode,
                $row->docUnit,
                $row->quantity,
                $row->docUnitPrice,
                $row->netAmount,
                $row->docCurrencyISO,
                $row->draftGrQuantity,
                $row->postedGrQuantity,
                $row->draftAPQuantity,
                $row->postedAPQuantity,
                $row->billedAmount,
                $row->openAPAmount,
                $row->remarks,
                $row->prRowIndentifer,
                $row->prNumber
            );

            $n = 0;
            foreach ($columnValues as $v) {

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

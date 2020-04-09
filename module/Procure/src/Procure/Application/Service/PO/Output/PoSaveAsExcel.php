<?php
namespace Procure\Application\Service\PO\Output;

use Application\Domain\Util\ExcelColumnMap;
use Procure\Application\Service\Output\AbstractRowFormatter;
use Procure\Application\Service\Output\AbstractSaveAsSpreadsheet;
use Procure\Domain\GenericDoc;
use Procure\Domain\PurchaseOrder\PORowSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PoSaveAsExcel extends AbstractSaveAsSpreadsheet
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\SaveAsInterface::saveMultiplyRowsAs()
     */
    public function saveMultiplyRowsAs($rows, AbstractRowFormatter $formatter)
    {
        if ($this->getSpreadSheetBuilder() == null) {
            return null;
        }

        if (count($rows) == 0) {
            return null;
        }

        // created header
        $params=[
        ];
        
        $this->getSpreadSheetBuilder()->setHeader($params);
        $objPHPExcel = $this->getSpreadSheetBuilder()->getPhpSpreadsheet();
        
        $header = 3;
        $i = 0;

        $cols = ExcelColumnMap::COLS;

        $headerValues = array(
            "#",
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

        foreach ($rows as $row) {

            $formatter->format($row);

            /**
             *
             * @var PORowSnapshot $row ;
             */

            $i ++;
            $l = $header + $i;

            $columnValues = array(
                $i,
                $row->vendorName,
                $row->docNumber,
                $row->itemSKU,
                $row->itemName,
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
                $row->getBilledAmount(),
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

        // created footer and export
        $params=[
        ];
        
        $this->getSpreadSheetBuilder()->setFooter($params);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Application\Service\Output\SaveAsInterface::saveDocAs()
     */
    public function saveDocAs(GenericDoc $doc, AbstractRowFormatter $formatter)
    {
        
        if ($this->getSpreadSheetBuilder() == null) {
            return null;
        }
        
        if (! $doc instanceof GenericDoc) {
            throw new \InvalidArgumentException(sprintf("Invalid input %s", "doc."));
        }

        if (count($doc->getDocRows()) == null) {
            return;
        }
        
        // Set Header
        
      
        $params=[
            "docNumber" =>$doc->getSysNumber()
        ];
        $this->getSpreadSheetBuilder()->setHeader($params);
        $objPHPExcel = $this->getSpreadSheetBuilder()->getPhpSpreadsheet();
        
        $header = 7;
        $i = 0;

        $cols = ExcelColumnMap::COLS;

        $headerValues = array(
            "#",
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

        $params=[
            "docNumber" =>$doc->getSysNumber()
        ];
        // created footer and export
        $this->getSpreadSheetBuilder()->setFooter($params);
    }
   
}

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
            "FA Remarks",
            "Status",
            "Row Ref",
            "Row#",
            "ItemSKU",
            "Item",
            "ItemSys",
            "Item Name",
            "Asset",
            "Unit",
            "Doc Cur",
            "Doc Qty",
            "Doc UP",
            "Net Amt",
            "Text Amt",
            "Gross Amt",

            "Item Vendor Name",
            "Item Vendor code",
            "Brand",

            "Std Qty",
            "Std UP",
            "Loc UP",
            "Loc Curr",

            "Draft GR",
            "Posted GR",
            "Posted AP",
            "Open Qty",
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
                $row->getFaRemarks(),
                $row->getTransactionStatus(),
                $row->getRowIdentifer(),
                $row->getRowNumber(),
                $row->getItemSKU(),
                $row->item,
                $row->getItemSysNumber(),
                $row->itemName,
                $row->getIsFixedAsset(),
                $row->docUnit,
                $row->getDocCurrencyISO(),
                $row->getDocQuantity(),
                $row->getDocUnitPrice(),

                $row->netAmount,
                $row->taxAmount,
                $row->grossAmount,

                $row->itemManufacturerModel,
                $row->vendorItemName,
                $row->vendorItemCode,
                $row->getBrand(),

                $row->draftGrQuantity,
                $row->postedGrQuantity,
                $row->draftAPQuantity,
                $row->postedAPQuantity,
                $row->getConfirmedGrBalance(),

                $row->billedAmount,
                $row->openAPAmount,
                $row->remarks,
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

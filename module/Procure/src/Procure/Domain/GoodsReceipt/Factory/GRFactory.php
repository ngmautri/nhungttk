<?php
namespace Procure\Domain\GoodsReceipt\Factory;

use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\Exception\PoInvalidOperationException;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\GoodsReceipt\GRRow;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PODocStatus;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRFactory
{

    /**
     *
     * @param PODoc $po
     */
    public static function createFromPO(PODoc $po)
    {
        if (!$po instanceof PODoc) {
            throw new InvalidArgumentException("PO Entity is required");
        }

        if ($po->getDocStatus() !== PODocStatus::DOC_STATUS_POSTED) {
            throw new PoInvalidOperationException("PO document is not posted!");
        }

        $rows = $po->getDocRows();

        if ($po->getDocRows() == null) {
            throw new PoInvalidOperationException("PO Entity  is empty!");
        }

        $gr = GRDoc::createFromPo($po);
        echo "\n" . $gr->getVendorName();

        foreach ($rows as $r) {
            $grRow = GrRow::createFromPoRow($r);
            echo sprintf("\n %s, PoRowId %s, %s" , $grRow->getItemName(), $grRow->getPoRow(), $grRow->getPrRow());
        }
        
        return $gr;
    }
}
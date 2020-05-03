<?php
namespace Inventory\Domain\Transaction\GR;

use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Transaction\TrxRow;
use Procure\Domain\GoodsReceipt\GRRow;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRFromPurchasingRow extends TrxRow
{

    public static function createFromPurchaseGrRow(GRRow $sourceObj, CommandOptions $options)
    {
        if (! $sourceObj instanceof GRRow) {
            throw new InvalidArgumentException("PO document is required!");
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var GRFromPurchasingRow $instance ;
         */
        $instance = new self();
        $instance = $sourceObj->convertTo($instance);

        // $instance->setDocType(Constants::PROCURE_DOC_TYPE_INVOICE_PO); // important.
        $instance->setGrRow($sourceObj->getId()); // Important

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $instance->initRow($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        return $instance;
    }
}
<?php
namespace Procure\Domain\GoodsReceipt;

use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Transaction\TrxRow;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GRReturnFromWHReturnRow extends GRRow
{

    public static function createFromWHReturnRow(GenericGR $rootEntity, TrxRow $sourceObj, CommandOptions $options)
    {
        if (! $sourceObj instanceof TrxRow) {
            throw new \InvalidArgumentException("PO document is required!");
        }
        if ($options == null) {
            throw new \InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var \Procure\Domain\GoodsReceipt\GRReturnFromWHReturnRow $instance
         */
        $instance = new self();

        $instance = $sourceObj->convertTo($instance);

        // Overwrite
        $instance->setGrRow($sourceObj->getId()); // Important
        $instance->setRemarks($instance->getRemarks() . \sprintf('[Auto.] ref. %s', $sourceObj->getRowIdentifer()));

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $instance->initRow($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        return $instance;
    }
}
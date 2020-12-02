<?php
namespace Inventory\Domain\Transaction\GI;

use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Domain\Transaction\Contracts\TrxFlow;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GIforReturnPORow extends TrxRow
{

    /**
     *
     * @param GenericTrx $rootEntity
     * @param TrxRow $sourceObj
     * @param CommandOptions $options
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Transaction\GI\GIforReturnPORow
     */
    public static function createFromGRFromPurchasingRow(GenericTrx $rootEntity, TrxRow $sourceObj, CommandOptions $options)
    {
        if (! $sourceObj instanceof TrxRow) {
            throw new InvalidArgumentException("TrxRow document is required!");
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var GIforReturnPORow $instance ;
         */
        $instance = new self();

        $instance = $sourceObj->convertTo($instance);

        // Important.
        $instance->setQuantity($sourceObj->getStockQty());
        $instance->setGrRow($sourceObj->getGrRow()); // PO-GR Row.
        $instance->setFlow(TrxFlow::WH_TRANSACTION_OUT);
        $instance->setWh($instance->getWarehouse());
        $instance->setRemarks($instance->getRemarks() . \sprintf('[Auto.] ref. %s', $sourceObj->getRowIdentifer()));

        // update PR and PO, AP if any
        $instance->initRow($options);

        return $instance;
    }
}
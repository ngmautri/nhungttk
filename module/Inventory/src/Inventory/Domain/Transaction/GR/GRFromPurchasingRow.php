<?php
namespace Inventory\Domain\Transaction\GR;

use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Domain\Transaction\Contracts\TrxFlow;
use Procure\Domain\GoodsReceipt\GRRow;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GRFromPurchasingRow extends TrxRow
{

    /**
     *
     * @param GenericTrx $rootEntity
     * @param GRRow $sourceObj
     * @param CommandOptions $options
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Transaction\GR\GRFromPurchasingRow
     */
    public static function createFromPurchaseGrRow(GenericTrx $rootEntity, GRRow $sourceObj, CommandOptions $options)
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

        $instance->setDocType($rootEntity->getDocType()); // important.
        $instance->setGrRow($sourceObj->getId()); // Important
        $instance->setFlow(TrxFlow::WH_TRANSACTION_IN);
        $instance->setWh($instance->getWarehouse());
        $instance->setRemarks($instance->getRemarks() . \sprintf('[Auto.] ref. %s', $sourceObj->getRowIdentifer()));

        // update PR and PO, AP if any
        $instance->setInvoiceRow($sourceObj->getApInvoiceRow());

        $instance->initRow($options);

        return $instance;
    }
}
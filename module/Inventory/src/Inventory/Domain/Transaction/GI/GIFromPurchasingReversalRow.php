<?php
namespace Inventory\Domain\Transaction\GI;

use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;
use Inventory\Domain\Transaction\Contracts\TrxFlow;
use Inventory\Domain\Transaction\GR\GRFromPurchasingRow;
use Procure\Domain\GoodsReceipt\GRRow;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class GIFromPurchasingReversalRow extends TrxRow
{

    /**
     *
     * @param GenericTrx $rootEntity
     * @param GRRow $sourceObj
     * @param CommandOptions $options
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Transaction\GR\GRFromPurchasingRow
     */
    public static function createFromPurchaseGrRowReversal(GenericTrx $rootEntity, GRRow $sourceObj, CommandOptions $options)
    {
        if (! $sourceObj instanceof GRRow) {
            throw new InvalidArgumentException("GR-PO document is required!");
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var GIFromPurchasingReversalRow $instance ;
         */
        $instance = new self();

        $instance = $sourceObj->convertTo($instance);
        $instance->setDocToken($rootEntity->getDocType());
        $instance->setGrRow($sourceObj->getId()); // Important
        $instance->setFlow($rootEntity->getMovementFlow());
        $instance->setWh($instance->getWarehouse());

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $instance->initRow($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        return $instance;
    }
}
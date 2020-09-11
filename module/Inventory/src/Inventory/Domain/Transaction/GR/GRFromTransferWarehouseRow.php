<?php
namespace Inventory\Domain\Transaction\GR;

use Application\Domain\Shared\Command\CommandOptions;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\TrxRow;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRFromTransferWarehouseRow extends TrxRow
{

    /**
     *
     * @param GenericTrx $rootEntity
     * @param TrxRow $sourceObj
     * @param CommandOptions $options
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Transaction\GR\GRFromTransferWarehouseRow
     */
    public static function createFromTORow(GenericTrx $rootEntity, TrxRow $sourceObj, CommandOptions $options)
    {
        if (! $rootEntity instanceof GenericTrx) {
            throw new InvalidArgumentException("GenericTrx document is required! " . \get_class($rootEntity));
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var GRFromTransferWarehouseRow $instance ;
         */
        $instance = new self();

        $instance = $sourceObj->convertTo($instance);

        // Overwrite
        $instance->setFlow($rootEntity->getMovementFlow());
        $instance->setWh($rootEntity->getWarehouse());
        $instance->setUnitPrice($sourceObj->getCogsLocal() / $sourceObj->getQuantity());

        $f = '[Auto.] WH-TO %s';
        $instance->setRemarks(\sprintf($f, $rootEntity->getWarehouse()));

        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $instance->initRow($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        return $instance;
    }

    public static function createFromGIRowReversal(GenericTrx $rootEntity, TrxRow $sourceObj, CommandOptions $options)
    {
        if (! $sourceObj instanceof GenericTrx) {
            throw new InvalidArgumentException("GenericTrx is required!");
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var GRFromTransferWarehouseRow $instance ;
         */
        $instance = new self();

        $instance = $sourceObj->convertTo($instance);
        $createdDate = new \Datetime();
        $createdBy = $options->getUserId();
        $instance->initRow($createdBy, date_format($createdDate, 'Y-m-d H:i:s'));

        return $instance;
    }
}
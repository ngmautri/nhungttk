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
class GRFromExchangeRow extends TrxRow
{

    /**
     *
     * @param GenericTrx $rootEntity
     * @param TrxRow $sourceObj
     * @param CommandOptions $options
     * @throws InvalidArgumentException
     * @return \Inventory\Domain\Transaction\GR\GRFromExchangeRow
     */
    public static function createFromGIRow(GenericTrx $rootEntity, TrxRow $sourceObj, CommandOptions $options)
    {
        if (! $rootEntity instanceof GenericTrx) {
            throw new InvalidArgumentException("GenericTrx document is required! " . \get_class($rootEntity));
        }
        if ($options == null) {
            throw new InvalidArgumentException("No Options is found");
        }

        /**
         *
         * @var GRFromExchangeRow $instance ;
         */
        $instance = new self();

        $instance = $sourceObj->convertTo($instance);

        // Overwrite
        $instance->setFlow($rootEntity->getMovementFlow());
        $instance->setWhLocation($rootEntity->getTartgetLocation());
        $instance->setRemarks($instance->getRemarks() . \sprintf('[Auto.] %s %s-%s', "Recycle Bin", $rootEntity->getWarehouse(), $rootEntity->getTartgetLocation()));

        $instance->initRow($options);

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
         * @var GRFromExchangeRow $instance ;
         */
        $instance = new self();

        $instance = $sourceObj->convertTo($instance);
        $instance->initRow($options);

        return $instance;
    }
}
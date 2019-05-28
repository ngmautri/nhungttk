<?php
namespace Inventory\Domain\Warehouse\Factory;

use Inventory\Domain\Exception\InvalidArgumentException;
use Inventory\Domain\Item\ItemSnapshot;
use Inventory\Domain\Warehouse\Transaction\WarehouseTransactionType;
use Inventory\Domain\Warehouse\Transaction\Factory\GIforCostCenterFactory;
use Inventory\Domain\Warehouse\Transaction\GenericWarehouseTransaction;
use Inventory\Domain\Warehouse\Transaction\WarehouseTransactionSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractTransactionFactory
{

    /**
     *
     * @var GenericWarehouseTransaction;
     */
    protected $warehouseTransaction;

    public static function getFactory($transactionTypeId)
    {
        switch ($transactionTypeId) {

            case WarehouseTransactionType::GI_FOR_COST_CENTER:
                $factory = new GIforCostCenterFactory();
                break;
            default:
                $factory = null;
                break;
        }

        return $factory;
    }

    /**
     *
     * @param \Inventory\Application\DTO\Warehouse\Transaction\WarehouseTransactionDTO $input;
     */
    public function createFromDTO($input)
    {

        /**
         * Abstract Method
         *
         * @var GenericWarehouseTransaction $trx
         */
        $trx = $this->createTransaction();

        $snapshot = new WarehouseTransactionSnapshot();

        $reflectionClass = new \ReflectionClass($input);
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (! is_object($property->getValue($input))) {

                if (property_exists($snapshot, $propertyName)) {

                    if ($property->getValue($input) == null || $property->getValue($input) == "") {
                        $snapshot->$propertyName = null;
                    } else {
                        $snapshot->$propertyName = $property->getValue($input);
                    }
                }
            }
        }

        // make from snapshot
        $trx->makeFromSnapshot($snapshot);

        /**
         * Abstract Method
         */
        $this->specifyTransaction();

        $notification = $trx->validate();

        if ($notification->hasErrors())
            throw new InvalidArgumentException($notification->errorMessage());

        return $trx;
    }

    abstract function createTransaction();

    abstract function specifyTransaction();
}
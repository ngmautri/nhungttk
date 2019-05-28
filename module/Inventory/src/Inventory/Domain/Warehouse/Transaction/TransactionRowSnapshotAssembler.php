<?php
namespace Inventory\Domain\Warehouse\Transaction;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionRowSnapshotAssembler
{

    public static function createFromSnapshotCode()
    {
        $itemSnapshot = new WarehouseTransactionRowSnapshot();
        $reflectionClass = new \ReflectionClass($itemSnapshot);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "\$this->" . $propertyName . " = \$snapshot->" . $propertyName . ";";
        }
    }

    /**
     *
     * @param WarehouseTransactionRow $obj
     * @return NULL|\Inventory\Domain\Warehouse\Transaction\WarehouseTransactionRowSnapshot
     */
    public static function createSnapshotFrom($obj)
    {
        if (! $obj instanceof WarehouseTransactionRow)
            return null;

        $snapShot = new WarehouseTransactionRowSnapshot();

        // should uss reflection object
        $reflectionClass = new \ReflectionObject($obj);
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (property_exists($snapShot, $propertyName)) {

                if ($property->getValue($obj) == null || $property->getValue($obj) == "") {
                    $snapShot->$propertyName = null;
                } else {
                    $snapShot->$propertyName = $property->getValue($obj);
                }
            }
        }

        return $snapShot;
    }
}

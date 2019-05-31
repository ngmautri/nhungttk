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
        $itemSnapshot = new TransactionRowSnapshot();
        $reflectionClass = new \ReflectionClass($itemSnapshot);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "\$this->" . $propertyName . " = \$snapshot->" . $propertyName . ";";
        }
    }
    
    /**
     * generete Mapping.
     */
    public static function createStoreMapping()
    {
        $entity = new \Application\Entity\NmtInventoryTrx();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "\$entity->set" . ucfirst($propertyName) . "(\$snapshot->" . $propertyName . ");";
        }
    }
    
    

   /**
    * 
    * @param unknown $obj
    * @return NULL|\Inventory\Domain\Warehouse\Transaction\TransactionRowSnapshot
    */
    public static function createSnapshotFrom($obj)
    {
        if (! $obj instanceof TransactionRow)
            return null;

        $snapShot = new TransactionRowSnapshot();

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

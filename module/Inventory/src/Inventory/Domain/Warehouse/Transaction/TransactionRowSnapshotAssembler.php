<?php
namespace Inventory\Domain\Warehouse\Transaction;


use Inventory\Application\DTO\Warehouse\Transaction\TransactionRowDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionRowSnapshotAssembler
{
    /**
     *
     * @param array $data
     * @return NULL|\Inventory\Domain\Warehouse\Transaction\TransactionSnapshot
     */
    public static function createSnapshotFromArray($data)
    {
        if ($data == null)
            return null;
            
            $snapShot = new TransactionRowSnapshot();
            
            foreach ($data as $property => $value) {
                if (property_exists($snapShot, $property)) {
                    $snapShot->$property = $value;
                }
            }
            return $snapShot;
    }
    
    /**
     * 
     * @param TransactionRowDTO $dto
     * @return NULL|\Inventory\Domain\Warehouse\Transaction\TransactionRowSnapshot
     */
    public static function createSnapshotFromDTO(TransactionRowDTO $dto)
    {
        if (! $dto instanceof TransactionRowDTO)
            return null;
            
            $snapShot = new TransactionRowSnapshot();
            
            $reflectionClass = new \ReflectionClass(get_class($dto));
            $properites = $reflectionClass->getProperties();
            
            foreach ($properites as $property) {
                $property->setAccessible(true);
                $propertyName = $property->getName();
                if (property_exists($snapShot, $propertyName)) {
                    $dto->$propertyName = $property->getValue($dto);
                }
            }
            return $snapShot;
    }

  
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
    * @param TransactionRow $obj
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
    
    /**
     *
     * @return array;
     */
    public static function findMissingProperties()
    {
        $missingProperties = array();
        $entity = new \Application\Entity\NmtInventoryTrx();
        $dto = new TransactionRowSnapshot();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (! property_exists($dto, $propertyName)) {
                $missingProperties[] = $propertyName;
            }
        }
        return $missingProperties;
    }
}

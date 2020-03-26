<?php
namespace Inventory\Domain\Warehouse\Transaction;

use Inventory\Application\DTO\Warehouse\Transaction\TransactionDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TransactionSnapshotAssembler
{

    /**
     *
     * @param TransactionSnapshot $snapShot
     * @param TransactionDTO $dto
     * @return NULL|\Inventory\Domain\Warehouse\Transaction\TransactionSnapshot
     */
    public static function updateSnapshotFromDTO($snapShot, $dto)
    {
        if (! $dto instanceof TransactionDTO || ! $snapShot instanceof TransactionSnapshot)
            return null;

        $reflectionClass = new \ReflectionClass($dto);
        $properites = $reflectionClass->getProperties();

        $changeableProperties = array(
            "movementType",
            "movementDate",
            "warehouse",
            "remarks"
        );

        /*
         * $dto->movementType;
         * $dto->movementDate;
         * $dto->warehouse;
         * $dto->remarks;
         */
        foreach ($properites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (property_exists($snapShot, $propertyName) && in_array($propertyName, $changeableProperties)) {

                if ($property->getValue($dto) == null || $property->getValue($dto) == "") {
                    $snapShot->$propertyName = null;
                } else {
                    $snapShot->$propertyName = $property->getValue($dto);
                }
            }
        }
        return $snapShot;
    }

    public static function createFromSnapshotCode()
    {
        $itemSnapshot = new TransactionSnapshot();
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
     * @param TransactionDTO $dto
     * @return NULL|\Inventory\Domain\Warehouse\Transaction\TransactionSnapshot
     */
    public static function createSnapshotFromDTO(TransactionDTO $dto)
    {
        if (! $dto instanceof TransactionDTO)
            return null;

        $snapShot = new TransactionSnapshot();

        $reflectionClass = new \ReflectionClass(get_class($dto));
        $properites = $reflectionClass->getProperties();

        foreach ($properites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (property_exists($snapShot, $propertyName)) {

                if ($property->getValue($dto) == null || $property->getValue($dto) == "") {
                    $snapShot->$propertyName = null;
                } else {
                    $snapShot->$propertyName = $property->getValue($dto);
                }
            }
        }
        return $snapShot;
    }

    /**
     *
     * @param array $data
     * @return NULL|\Inventory\Domain\Warehouse\Transaction\TransactionSnapshot
     */
    public static function createSnapshotFromArray($data)
    {
        if ($data == null)
            return null;

        $snapShot = new TransactionSnapshot();

        foreach ($data as $property => $value) {
            if (property_exists($snapShot, $property)) {
                $snapShot->$property = $value;
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
        $entity = new \Application\Entity\NmtInventoryMv();
        $dto = new TransactionSnapshot();
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

    /**
     *
     * @param GenericTransaction $obj
     * @return NULL|\Inventory\Domain\Warehouse\Transaction\TransactionSnapshot
     */
    public static function createSnapshotFrom($obj)
    {
        if (! $obj instanceof GenericTransaction) {
            return null;
        }

        $snapShot = new TransactionSnapshot();

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

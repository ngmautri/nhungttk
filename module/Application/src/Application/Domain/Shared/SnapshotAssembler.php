<?php
namespace Application\Domain\Shared;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class SnapshotAssembler
{

    /**
     *
     * @param array $data
     * @param object $snapshot
     * @return object
     */
    public static function createSnapShotFromArray($data, $snapshot)
    {
        foreach ($data as $property => $value) {
            if (property_exists($snapshot, $property)) {
                if ($value == null || $value == "") {
                    $snapshot->$property = null;
                } else {
                    $snapshot->$property = $value;
                }
            }
        }
        return $snapshot;
    }

    /**
     *
     * @param array $data
     * @param object $snapshot
     * @return object
     */
    public static function createSnapShotFromDTO($dto, $snapShot)
    {
        if ($dto == null or $snapShot == null) {
            return null;
        }

        $reflectionClass = new \ReflectionClass(get_class($dto));
        $itemProperites = $reflectionClass->getProperties();

        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (property_exists($snapShot, $propertyName)) {
                $snapShot->$propertyName = $property->getValue($dto);
            }
        }
        
        return $snapShot;
    }

    /**
     *
     * @param object $obj
     * @param object $snapshot
     */
    public static function createSnapshotFrom($obj, $snapshot)
    {
        if ($obj == null or $snapshot == null) {
            return null;
        }

        $reflectionClass = new \ReflectionObject($obj);
        $properites = $reflectionClass->getProperties();

        $refObject = new \ReflectionObject($snapshot);

        foreach ($properites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (property_exists($snapshot, $propertyName)) {

                $refProperty = $refObject->getProperty($propertyName);
                $refProperty->setAccessible(true);

                if ($property->getValue($obj) == null || $property->getValue($obj) == "") {
                    $refProperty->setValue($snapshot, null);
                } else {
                    $refProperty->setValue($snapshot, $property->getValue($obj));
                }
            }
        }

        return $snapshot;
    }

    /**
     *
     * @param object $obj
     * @param object $snapshot
     */
    public static function makeFromSnapshot($obj, $snapshot)
    {
        if ($obj == null or $snapshot == null) {
            return;
        }

        // should use reflection object
        $reflectionClass = new \ReflectionObject($snapshot);
        $properites = $reflectionClass->getProperties();

        $refObject = new \ReflectionObject($obj);

        foreach ($properites as $property) {

            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (property_exists($obj, $propertyName)) {

                $refProperty = $refObject->getProperty($propertyName);
                $refProperty->setAccessible(true);

                if ($property->getValue($snapshot) == null || $property->getValue($snapshot) == "") {
                    $refProperty->setValue($obj, null);
                } else {

                    $refProperty->setValue($obj, $property->getValue($snapshot));
                }
            }
        }
    }
}

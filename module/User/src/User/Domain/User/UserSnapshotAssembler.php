<?php
namespace User\Domain\User;

use Inventory\Application\DTO\Warehouse\WarehouseDTOAssembler;
use Inventory\Domain\Warehouse\GenericWarehouse;
use User\Application\DTO\User\UserDTO;
use User\Application\DTO\User\UserDTOAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UserSnapshotAssembler
{

    public static function findMissingPropsInEntity()
    {
        $missingProperties = array();
        $baseObj = new BaseUser();

        $reflectionClass = new \ReflectionClass($baseObj);
        $baseProps = $reflectionClass->getProperties();

        $entity = WarehouseDTOAssembler::getEntity();

        foreach ($baseProps as $property) {
            $propertyName = $property->getName();
            if (! property_exists($entity, $propertyName)) {
                echo (sprintf("\n protected $%s;", $propertyName));
                $missingProperties[] = $propertyName;
            }
        }
        return $missingProperties;
    }

    public static function findMissingDBPropsInBase()
    {
        $missingProperties = array();

        $entityProps = UserDTOAssembler::createDTOProperities();
        $dto = new BaseUser();

        foreach ($entityProps as $property) {
            $propertyName = $property->getName();
            if (! property_exists($dto, $propertyName)) {
                echo (sprintf("\n protected $%s;", $propertyName));
                $missingProperties[] = $propertyName;
            }
        }
        return $missingProperties;
    }

    public static function createProperities()
    {
        $entity = new UserSnapshot();
        $reflectionClass = new \ReflectionClass($entity);
        $props = $reflectionClass->getProperties();
        foreach ($props as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "protected $" . $propertyName . ";";
        }
    }

    /**
     *
     * @return array;
     */
    public static function findMissingPropertiesOfSnapshot()
    {
        $missingProperties = array();
        $entity = new GenericUser();
        $dto = new UserSnapshot();

        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (! property_exists($dto, $propertyName)) {
                echo (sprintf("\n protected $%s;", $propertyName));

                $missingProperties[] = $propertyName;
            }
        }
        return $missingProperties;
    }

    /**
     *
     * @param UserSnapshot $snapShot
     * @param UserDTO $dto
     * @return NULL|\User\Domain\User\UserSnapshot
     */
    public static function updateSnapshotFromDTO(UserSnapshot $snapShot, UserDTO $dto)
    {
        if (! $dto instanceof UserDTO || ! $snapShot instanceof UserSnapshot)
            return null;

        $reflectionClass = new \ReflectionClass($dto);
        $itemProperites = $reflectionClass->getProperties();

        $excludedProperties = array(
            "id",
            "uuid",
            "token",
            "checksum",
            "createdBy",
            "createdOn",
            "lastChangeOn",
            "lastChangeBy",
            "sysNumber",
            "company",
            "location",
            "revisionNo"
        );

        $changeableProperties = array(
            "movementType",
            "movementDate",
            "warehouse",
            "remarks"
        );

        // $dto->isSparepart;

        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (property_exists($snapShot, $propertyName) && ! in_array($propertyName, $excludedProperties)) {

                if ($property->getValue($dto) == null || $property->getValue($dto) == "") {
                    $snapShot->$propertyName = null;
                } else {
                    $snapShot->$propertyName = $property->getValue($dto);
                }
            }

            /*
             * if (property_exists($snapShot, $propertyName) && in_array($propertyName, $changeableProperties)) {
             *
             * if ($property->getValue($dto) == null || $property->getValue($dto) == "") {
             * $snapShot->$propertyName = null;
             * } else {
             * $snapShot->$propertyName = $property->getValue($dto);
             * }
             * }
             */
        }
        return $snapShot;
    }

    /**
     *
     * @param GenericWarehouse $obj
     * @return NULL|\Inventory\Domain\Item\ItemSnapshot
     */
    public static function createSnapshotFrom($obj)
    {
        if (! $obj instanceof GenericUser) {
            return null;
        }

        $snapShot = new UserSnapshot();

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

<?php
namespace Procure\Domain\PurchaseOrder;

use Procure\Application\DTO\Po\PORowDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PORowSnapshotAssembler
{

    /**
     *
     * @return LocationSnapshot;
     */
    public static function createFromSnapshotCode()
    {
        $itemSnapshot = new PORowSnapshot();
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
    * @param array $data
    * @return NULL|\Procure\Domain\PurchaseRequest\PORowSnapshot
    */
    public static function createSnapshotFromArray($data)
    {
        if ($data == null)
            return null;

            $snapShot = new PORowSnapshot();

        foreach ($data as $property => $value) {
            if (property_exists($snapShot, $property)) {

                if ($value == null || $value == "") {
                    $snapShot->$property = null;
                } else {
                    $snapShot->$property = $value;
                }
            }
        }
        return $snapShot;
    }

    /**
     * 
     * @param PORowDTO $dto
     * @return NULL|\Procure\Domain\PurchaseRequest\PORowSnapshot
     */
    public static function createSnapshotFromDTO(PORowDTO $dto)
    {
        if (! $dto instanceof PORowDTO)
            return null;

        $snapShot = new PORowSnapshot();

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
    * @param PORowSnapshot $snapShot
    * @param PORowDTO $dto
    * @return NULL|\Procure\Domain\PurchaseRequest\PORowSnapshot
    */
    public static function updateSnapshotFromDTO(PORowSnapshot $snapShot, PORowDTO $dto)
    {
        if (! $dto instanceof PORowDTO || ! $snapShot instanceof PORowSnapshot)
            return null;

        $reflectionClass = new \ReflectionClass($dto);
        $itemProperites = $reflectionClass->getProperties();

        /**
         * Fields, that are update automatically
         *
         * @var array $excludedProperties
         */
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
            "itemType",
            "revisionNo",
            "isStocked",
            "isFixedAsset",
            "isSparepart",
            "itemTypeId"
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
        }
        return $snapShot;
    }

   /**
    * 
    * @param PORow $obj
    * @return NULL|\Procure\Domain\PurchaseRequest\PRrowSnapshot
    */
    public static function createSnapshotFrom(PORow $obj)
    {
        if (! $obj instanceof PORow) {
            return null;
        }

        $snapShot = new PRrowSnapshot();

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

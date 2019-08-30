<?php
namespace Procure\Domain\PurchaseRequest;

use Procure\Application\DTO\Pr\PrDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PRSnapshotAssembler
{

    /**
     *
     * @return LocationSnapshot;
     */
    public static function createFromSnapshotCode()
    {
        $itemSnapshot = new PRSnapshot();
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
     * @return NULL|\Procure\Domain\PurchaseRequest\PRSnapshot
     */
    public static function createSnapshotFromArray($data)
    {
        if ($data == null)
            return null;

        $snapShot = new PRSnapshot();

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
     * @param PrDTO $dto
     * @return NULL|\Procure\Domain\PurchaseRequest\PRSnapshot
     */
    public static function createSnapshotFromDTO(PrDTO $dto)
    {
        if (! $dto instanceof PrDTO)
            return null;

        $snapShot = new PRSnapshot();

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
     * @param PRSnapshot $snapShot
     * @param PrDTO $dto
     * @return NULL|\Procure\Domain\PurchaseRequest\PRSnapshot
     */
    public static function updateSnapshotFromDTO(PRSnapshot $snapShot, PrDTO $dto)
    {
        if (! $dto instanceof PrDTO || ! $snapShot instanceof PRSnapshot)
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
     * @param GenericPR $obj
     * @return NULL|\Procure\Domain\PurchaseRequest\PRSnapshot
     */
    public static function createSnapshotFrom(GenericPR $obj)
    {
        if (! $obj instanceof GenericPR) {
            return null;
        }

        $snapShot = new PRSnapshot();

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

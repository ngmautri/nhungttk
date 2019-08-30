<?php
namespace Procure\Domain\APInvoice;

use Procure\Application\DTO\Ap\APDocDTO;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APDocSnapshotAssembler
{

    /**
     *
     * @return LocationSnapshot;
     */
    public static function createFromSnapshotCode()
    {
        $itemSnapshot = new APDocSnapshot();
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
     * @return APDocSnapshot
     */
    public static function createSnapshotFromArray($data)
    {
        if ($data == null)
            return null;

            $snapShot = new APDocSnapshot();

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
     * @param APDocDTO $dto
     * @return NULL|\Procure\Domain\APInvoice\APDocSnapshot
     */
    public static function createSnapshotFromDTO(APDocDTO $dto)
    {
        if (! $dto instanceof APDocDTO)
            return null;

            $snapShot = new APDocSnapshot();

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
     * @param APDocSnapshot $snapShot
     * @param APDocDTO $dto
     * @return NULL|\Procure\Domain\APInvoice\APDocSnapshot
     */
    public static function updateSnapshotFromDTO(APDocSnapshot $snapShot, APDocDTO $dto)
    {
        if (! $dto instanceof APDocDTO || ! $snapShot instanceof APDocSnapshot)
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
     * @param GenericAPDoc $objGenericAPDoc
     * @return NULL|\Procure\Domain\APInvoice\APDocSnapshot
     */
    public static function createSnapshotFrom(GenericAPDoc $obj)
    {
        if (! $obj instanceof GenericAPDoc) {
            return null;
        }

        $snapShot = new APDocSnapshot();

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

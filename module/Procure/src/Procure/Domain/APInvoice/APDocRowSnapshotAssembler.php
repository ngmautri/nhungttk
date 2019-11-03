<?php
namespace Procure\Domain\APInvoice;

use Procure\Application\DTO\Ap\APDocRowDTO;


/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class APDocRowSnapshotAssembler
{
    
    
    /**
     * generete fields.
     */
    public static function createProperities()
    {
        $entity = new APDocRowDetailsSnapshot();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "protected $" . $propertyName . ";";
        }
    }

    /**
     *
     * @return LocationSnapshot;
     */
    public static function createFromSnapshotCode()
    {
        $itemSnapshot = new APDocRowSnapshot();
        $reflectionClass = new \ReflectionClass($itemSnapshot);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            //print "\n" . "\$this->" . $propertyName . " = \$snapshot->" . $propertyName . ";";
            
            print "\n" . "\$targetSnapShot->" . $propertyName . " = \$sourceSnapShot-;";
        }
    }
    
    /**
     *
     * @return LocationSnapshot;
     */
    public static function createSnapshotDetailsCode()
    {
        $itemSnapshot = new APDocRowDetailsSnapshot();
        $reflectionClass = new \ReflectionClass($itemSnapshot);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            //print "\n" . "\$this->" . $propertyName . " = \$snapshot->" . $propertyName . ";";
            
            print "\n" . "\$snapShot->" . $propertyName . " = ";
        }
    }

    /**
     *
     * @param array $data
     * @return APDocRowSnapshot
     */
    public static function createSnapshotFromArray($data)
    {
        if ($data == null)
            return null;

            $snapShot = new APDocRowSnapshot();

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
     * @param APDocRowDTO $dto
     * @return NULL|\Procure\Domain\APInvoice\APDocRowSnapshot
     */
    public static function createSnapshotFromDTO(APDocRowDTO $dto)
    {
        if (! $dto instanceof APDocRowDTO)
            return null;

            $snapShot = new APDocRowSnapshot();

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
     * @param APDocRowSnapshot $snapShot
     * @param APDocRowDTO $dto
     * @return NULL|\Procure\Domain\APInvoice\APDocRowSnapshot
     */
    public static function updateSnapshotFromDTO(APDocRowSnapshot $snapShot, APDocRowDTO $dto)
    {
        if (! $dto instanceof APDocRowDTO || ! $snapShot instanceof APDocRowSnapshot)
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
     * @param APDocRow $obj
     * @return NULL|\Procure\Domain\APInvoice\APDocRowSnapshot
     */
    public static function createSnapshotFrom(APDocRow $obj)
    {
        if (! $obj instanceof APDocRow) {
            return null;
        }

        $snapShot = new APDocRowSnapshot();

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

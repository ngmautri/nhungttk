<?php
namespace Inventory\Domain\Item;

use Application\Domain\Shared\AbstractDTO;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Inventory\Application\DTO\Item\ItemDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemSnapshotAssembler
{

    const AUTO_GENERATED_FIELDS = [
        "movementDate",
        "isActive",
        "warehouse",
        "targetWarehouse",
        "remarks"
    ];

    private static $defaultIncludedFields = [
        "movementDate",
        "isActive",
        "warehouse",
        "targetWarehouse",
        "remarks"
    ];

    private static $defaultExcludedFields = [
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
        "qoList",
        "procureGrList",
        "batchNoList",
        "fifoLayerConsumeList",
        "stockGrList",
        "pictureList",
        "attachmentList",
        "prList",
        "poList",
        "apList",
        "serialNoList",
        "batchList",
        "fifoLayerList",
        "backwardAssociationList",
        "associationList"
    ];

    public static function createFromQueryHit($hit)
    {
        if ($hit == null) {
            return;
        }

        $snapshort = new ItemSnapshot();
        $reflectionClass = new \ReflectionClass($snapshort);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();

            if ($hit->__isset($propertyName)) {
                $snapshort->$propertyName = $hit->$propertyName;
            }
        }

        $snapshort->id = $hit->item_id; // important

        return $snapshort;
    }

    public static function createIndexDoc()
    {
        $entity = new ItemSnapshot();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();

            $v = \sprintf("\$snapshot->get%s()", ucfirst($propertyName));

            print \sprintf("\n\$doc->addField(Field::text('%s', %s));", $propertyName, $v);
        }
    }

    // Snapshot from Array
    // =============================
    public static function updateAllFieldsFromArray(AbstractDTO $snapShot, $data)
    {
        return GenericObjectAssembler::updateAllFieldsFromArray($snapShot, $data);
    }

    public static function updateIncludedFieldsFromArray(AbstractDTO $snapShot, $data, $fields)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($snapShot, $data, $fields);
    }

    public static function updateDefaultIncludedFieldsFromArray(AbstractDTO $snapShot, $data)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($snapShot, $data, self::$defaultIncludedFields);
    }

    public static function updateExcludedFieldsFromArray(AbstractDTO $snapShot, $data, $fields)
    {
        return GenericObjectAssembler::updateExcludedFieldsFromArray($snapShot, $data, $fields);
    }

    public static function updateDefaultExcludedFieldsFromArray(AbstractDTO $snapShot, $data)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($snapShot, $data, self::$defaultExcludedFields);
    }

    // Snapshot from Object
    // =============================
    public static function updateAllFieldsFrom(AbstractDTO $snapShot, $data)
    {
        return GenericObjectAssembler::updateAllFieldsFrom($snapShot, $data);
    }

    public static function updateIncludedFieldsFrom(AbstractDTO $snapShot, $data, $fields)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($snapShot, $data, $fields);
    }

    public static function updateDefaultFieldsFrom(AbstractDTO $snapShot, $data)
    {
        return GenericObjectAssembler::updateIncludedFieldsFrom($snapShot, $data, self::$defaultIncludedFields);
    }

    public static function updateExcludedFieldsFrom(AbstractDTO $snapShot, $data, $fields)
    {
        return GenericObjectAssembler::updateExcludedFieldsFromArray($snapShot, $data, $fields);
    }

    public static function updateDefaultExcludedFieldsFrom(AbstractDTO $snapShot, $data)
    {
        return GenericObjectAssembler::updateExcludedFieldsFrom($snapShot, $data, self::$defaultExcludedFields);
    }

    // Entity from Object
    // =============================
    public static function updateEntityExcludedDefaultFieldsFrom(GenericItem $entity, $data)
    {
        return GenericObjectAssembler::updateExcludedFieldsFrom($entity, $data, self::$defaultExcludedFields);
    }

    public static function updateEntityAllFieldsFrom(GenericItem $entity, $data)
    {
        return GenericObjectAssembler::updateAllFieldsFrom($entity, $data);
    }

    /**
     *
     * @deprecated
     * @param ItemSnapshot $snapShot
     * @param ItemDTO $dto
     * @param array $editableProperties
     * @return NULL|\Inventory\Domain\Item\ItemSnapshot
     */
    public static function updateSnapshotFieldsFromDTO(ItemSnapshot $snapShot, ItemDTO $dto, $editableProperties)
    {
        if ($dto == null || ! $snapShot instanceof ItemSnapshot || $editableProperties == null)
            return null;

        $reflectionClass = new \ReflectionClass($dto);
        $props = $reflectionClass->getProperties();

        foreach ($props as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (property_exists($snapShot, $propertyName) && in_array($propertyName, $editableProperties)) {

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
     * @deprecated
     * @param ItemSnapshot $snapShot
     * @param ItemDTO $dto
     * @param array $excludedProperties
     * @return NULL|\Inventory\Domain\Item\ItemSnapshot
     */
    public static function updateSnapshotFromDTOExcludeFields(ItemSnapshot $snapShot, ItemDTO $dto, $excludedProperties)
    {
        if ($dto == null || ! $snapShot instanceof ItemSnapshot || $excludedProperties == null) {
            return null;
        }

        $reflectionClass = new \ReflectionClass($dto);
        $props = $reflectionClass->getProperties();

        foreach ($props as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();

            if (in_array($propertyName, self::AUTO_GENERATED_FIELDS)) {
                continue;
            }

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

    public static function updateSnapshotFromDTO($snapShot, $dto)
    {
        if (! $dto instanceof ItemDTO || ! $snapShot instanceof ItemSnapshot)
            return null;

        $reflectionClass = new \ReflectionClass($dto);
        $props = $reflectionClass->getProperties();

        foreach ($props as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            if (property_exists($snapShot, $propertyName) && ! in_array($propertyName, self::AUTO_GENERATED_FIELDS)) {

                if ($property->getValue($dto) == null || $property->getValue($dto) == "") {
                    $snapShot->$propertyName = null;
                } else {
                    $snapShot->$propertyName = $property->getValue($dto);
                }
            }
        }
        return $snapShot;
    }
}

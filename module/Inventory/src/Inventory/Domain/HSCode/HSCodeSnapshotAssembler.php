<?php
namespace Inventory\Domain\HSCode;

use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Application\DTO\Item\ItemDTOAssembler;
use Inventory\Domain\Item\BaseItem;
use Inventory\Domain\Item\ItemSnapshot;
use Procure\Domain\GenericDoc;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class HSCodeSnapshotAssembler
{

    const EXCLUDED_FIELDS = 1;

    const EDITABLE_FIELDS = 2;

    const AUTO_GENERATED_FIELDS = [
        "id",
        "isStocked",
        "isFixedAsset",
        "isSparepart",
        "createdOn",
        "lastPurchasePrice",
        "lastPurchaseCurrency",
        "lastPurchaseDate",
        "lastChangeOn",
        "token",
        "checksum",
        "sysNumber",
        "revisionNo",
        "avgUnitPrice",
        "uuid",
        "createdBy",
        "lastChangeBy",
        "company",

        "lastPrRow",
        "lastPoRow",
        "lastApInvoiceRow",
        "lastTrxRow",
        "lastPurchasing",
        "itemType",

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
        "fifoLayerList"
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

    /**
     *
     * @return array;
     */
    public static function findMissingPropertiesOfSnapshot()
    {
        $missingProperties = array();
        $entity = new GenericDoc();
        $dto = new ItemSnapshot();

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

    public static function findMissingPropsInEntity()
    {
        $missingProperties = array();
        $baseObj = new BaseItem();

        $reflectionClass = new \ReflectionClass($baseObj);
        $baseProps = $reflectionClass->getProperties();

        $entity = ItemDTOAssembler::getEntity();

        foreach ($baseProps as $property) {
            $propertyName = $property->getName();
            if (! property_exists($entity, $propertyName)) {
                echo (sprintf("\n protected $%s;", $propertyName));
                $missingProperties[] = $propertyName;
            }
        }
        return $missingProperties;
    }

    /**
     *
     * @return array;
     */
    public static function findMissingPropsInBaseItem()
    {
        $missingProperties = array();

        $entityProps = ItemDTOAssembler::createDTOProperities();
        $dto = new BaseItem();

        foreach ($entityProps as $property) {
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

    /**
     * generete fields.
     */
    public static function createProperities()
    {
        $entity = new ItemSnapshot();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "protected $" . $propertyName . ";";
        }
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

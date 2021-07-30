<?php
namespace Procure\Domain\QuotationRequest;

use Application\Domain\Shared\AbstractDTO;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Procure\Domain\QuotationRequest\Definition\QrRowDefinition;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class QRRowSnapshotAssembler
{

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
        return GenericObjectAssembler::updateIncludedFieldsFromArray($snapShot, $data, QrRowDefinition::$defaultIncludedFields);
    }

    public static function updateExcludedFieldsFromArray(AbstractDTO $snapShot, $data, $fields)
    {
        return GenericObjectAssembler::updateExcludedFieldsFromArray($snapShot, $data, $fields);
    }

    public static function updateDefaultExcludedFieldsFromArray(AbstractDTO $snapShot, $data)
    {
        return GenericObjectAssembler::updateIncludedFieldsFromArray($snapShot, $data, QrRowDefinition::$defaultExcludedFields);
    }

    // from Object
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
        return GenericObjectAssembler::updateIncludedFieldsFrom($snapShot, $data, QrRowDefinition::$defaultIncludedFields);
    }

    public static function updateExcludedFieldsFrom(AbstractDTO $snapShot, $data, $fields)
    {
        return GenericObjectAssembler::updateExcludedFieldsFromArray($snapShot, $data, $fields);
    }

    public static function updateDefaultExcludedFieldsFrom(AbstractDTO $snapShot, $data)
    {
        return GenericObjectAssembler::updateExcludedFieldsFrom($snapShot, $data, QrRowDefinition::$defaultExcludedFields);
    }

    public static function createFromQueryHit($hit)
    {
        if ($hit == null) {
            return;
        }

        $snapshort = new QRRowSnapshot();
        $reflectionClass = new \ReflectionClass($snapshort);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();

            if ($hit->__isset($propertyName)) {
                $snapshort->$propertyName = $hit->$propertyName;
            }
        }

        $snapshort->id = $hit->rowId; // important
        if ($hit->__isset("itemId")) {
            $snapshort->item = $hit->itemId; // important
        }

        return $snapshort;
    }
}

<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Application\Domain\Shared\AbstractDTO;
use Procure\Application\DTO\Po\PORowDetailsDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class PORowSnapshotAssembler
{

    private static $defaultExcludedProperties = array(
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
        "currencyIso3",
        "vendorName",
        "docStatus",
        "workflowStatus",
        "transactionStatus",
        "paymentStatus"
    );

    private static $defaultEditableProperties = array(
        "isActive",
        "remarks",
        "rowNumber",
        "item",
        "prRow",
        "vendorItemCode",
        "vendorItemName",
        "docQuantity",
        "docUnit",
        "docUnitPrice",
        "conversionFactor",
        "descriptionText",
        "taxRate"
    );

    const EXCLUDED_FIELDS = 1;

    const EDITABLE_FIELDS = 2;

    public static function createIndexDoc()
    {
        $entity = new PORowSnapshot();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();

            $v = \sprintf("\$row->get%s()", ucfirst($propertyName));

            print \sprintf("\n\$doc->addField(Field::text('%s', %s));", $propertyName, $v);
        }
    }

    public static function createFromQueryHit($hit)
    {
        if ($hit == null) {
            return;
        }

        $snapshort = new PORowSnapshot();
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

    /**
     *
     * @param array $data
     * @return NULL|\Procure\Domain\PurchaseOrder\PORowSnapshot
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
     * @param Object $dto
     * @return NULL|\Procure\Domain\PurchaseOrder\PORowSnapshot
     */
    public static function createSnapshotFromDTO($dto)
    {
        if ($dto == null)
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
     * @param Object $dto
     * @return NULL|\Procure\Domain\PurchaseOrder\PORowSnapshot
     */
    public static function updateSnapshotFromDTO(PORowSnapshot $snapShot, $dto, $editMode = self::EDITABLE_FIELDS)
    {
        if ($dto == null || ! $snapShot instanceof PORowSnapshot)
            return null;

        $reflectionClass = new \ReflectionClass($dto);
        $props = $reflectionClass->getProperties();

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
            "currencyIso3",
            "vendorName",
            "docStatus",
            "workflowStatus",
            "transactionStatus",
            "paymentStatus"
        );

        $editableProperties = array(
            "isActive",
            "vendor",
            "contractNo",
            "contractDate",
            "docCurrency",
            "exchangeRate",
            "incoterm",
            "incotermPlace",
            "paymentTerm",
            "remarks"
        );

        /**
         *
         * @var PORowDetailsDTO $dto ;
         */

        foreach ($props as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();

            if ($editMode == self::EXCLUDED_FIELDS) {
                if (property_exists($snapShot, $propertyName) && ! in_array($propertyName, $excludedProperties)) {

                    if ($property->getValue($dto) == null || $property->getValue($dto) == "") {
                        $snapShot->$propertyName = null;
                    } else {
                        $snapShot->$propertyName = $property->getValue($dto);
                    }
                }
            }

            if ($editMode == self::EDITABLE_FIELDS) {
                if (property_exists($snapShot, $propertyName) && in_array($propertyName, $editableProperties)) {

                    if ($property->getValue($dto) == null || $property->getValue($dto) == "") {
                        $snapShot->$propertyName = null;
                    } else {
                        $snapShot->$propertyName = $property->getValue($dto);
                    }
                }
            }
        }
        return $snapShot;
    }

    public static function updateSnapshotFieldsFromArray(AbstractDTO $snapShot, $data, $editableProperties = null)
    {
        if ($editableProperties == null) {
            $editableProperties = self::$defaultEditableProperties;
        }

        return GenericSnapshotAssembler::updateSnapshotFieldsFromArray($snapShot, $data, $editableProperties);
    }

    /**
     *
     * @param PORowSnapshot $snapShot
     * @param object $dto
     * @param array $editableProperties
     * @return NULL|\Procure\Domain\PurchaseOrder\PORowSnapshot
     */
    public static function updateSnapshotFieldsFromDTO(PORowSnapshot $snapShot, $dto, $editableProperties)
    {
        if ($dto == null || ! $snapShot instanceof PORowSnapshot || $editableProperties == null)
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
}

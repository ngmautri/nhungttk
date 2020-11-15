<?php
namespace Procure\Domain\PurchaseOrder;

use Application\Domain\Shared\AbstractDTO;
use Procure\Application\DTO\Po\PoDTO;
use Application\Application\Contracts\GenericSnapshotAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class POSnapshotAssembler
{

    private static $defaultEditableProperties = array(
        "isActive",
        "vendor",
        "contractNo",
        "contractDate",
        "docDate",
        "docNumber",
        "docCurrency",
        "exchangeRate",
        "incoterm",
        "incotermPlace",
        "paymentTerm",
        "remarks",
        "pmtTerm"
    );

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
        "paymentStatus",
        "paymentStatus"
    );

    public static function updateSnapshotFieldsFromArray(AbstractDTO $snapShot, $data, $editableProperties = null)
    {
        if ($editableProperties == null) {
            $editableProperties = self::$defaultEditableProperties;
        }

        return GenericSnapshotAssembler::updateSnapshotFieldsFromArray($snapShot, $data, $editableProperties);
    }

    /**
     *
     * @deprecated
     * @param POSnapshot $snapShot
     * @param PoDTO $dto
     * @param array $editableProperties
     * @return NULL|\Procure\Domain\PurchaseOrder\POSnapshot
     */
    public static function updateSnapshotFieldsFromDTO(POSnapshot $snapShot, $dto, $editableProperties)
    {
        if ($dto == null || ! $snapShot instanceof POSnapshot || $editableProperties == null)
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
     * @param array $data
     * @return NULL|\Procure\Domain\PurchaseOrder\POSnapshot
     */
    public static function createSnapshotFromArray($data)
    {
        if ($data == null)
            return null;

        $snapShot = new POSnapshot();

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
     * @deprecated
     * @param PoDTO $dto
     * @return NULL|\Procure\Domain\PurchaseOrder\POSnapshot
     */
    public static function createSnapshotFromDTO(PoDTO $dto)
    {
        if (! $dto instanceof PoDTO)
            return null;

        $snapShot = new POSnapshot();

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
     * @deprecated
     * @param POSnapshot $snapShot
     * @param PoDTO $dto
     * @return NULL|\Procure\Domain\PurchaseOrder\POSnapshot
     */
    public static function updateSnapshotFromDTO(PoDTO $dto, POSnapshot $snapShot, $editMode = self::EDITABLE_FIELDS)
    {
        if (! $dto instanceof PoDTO || ! $snapShot instanceof POSnapshot)
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
            "paymentStatus",
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
            "remarks",
            "pmtTerm"
        );

        // $snapShot->getPay;

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
}

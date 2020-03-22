<?php
namespace Procure\Domain\GoodsReceipt;

use Procure\Application\DTO\Po\PoDTO;
use Procure\Application\DTO\Gr\GrDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRSnapshotAssembler
{

    const EXCLUDED_FIELDS = 1;

    const EDITABLE_FIELDS = 2;

    /**
     *
     * @param GRSnapshot $snapShot
     * @param object $dto
     * @param array $editableProperties
     * @return NULL|\Procure\Domain\GoodsReceipt\GRSnapshot
     */
    public static function updateSnapshotFieldsFromDTO(GRSnapshot $snapShot, $dto, $editableProperties)
    {
        if ($dto == null || ! $snapShot instanceof GRSnapshot || $editableProperties == null)
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
     * generete fields.
     */
    public static function createProperities()
    {
        $entity = new GRDetailsSnapshot();
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
     * @param array $data
     * @return NULL|\Procure\Domain\PurchaseOrder\POSnapshot
     */
    public static function createSnapshotFromArray($data)
    {
        if ($data == null)
            return null;

        $snapShot = new GRSnapshot();

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
     * '
     *
     * @param GrDTO $dto
     * @return NULL|\Procure\Domain\GoodsReceipt\GRSnapshot
     */
    public static function createSnapshotFromDTO(GrDTO $dto)
    {
        if (! $dto instanceof PoDTO)
            return null;

        $snapShot = new GRSnapshot();

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
     * @param GrDTO $dto
     * @param GRSnapshot $snapShot
     * @param int $editMode
     * @return NULL|\Procure\Domain\GoodsReceipt\GRSnapshot
     */
    public static function updateSnapshotFromDTO(GrDTO $dto, GRSnapshot $snapShot, $editMode = self::EDITABLE_FIELDS)
    {
        if (! $dto instanceof GrDTO || ! $snapShot instanceof GRSnapshot)
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

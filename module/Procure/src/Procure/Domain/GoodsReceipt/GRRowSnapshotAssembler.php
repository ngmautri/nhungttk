<?php
namespace Procure\Domain\GoodsReceipt;

use Procure\Application\DTO\Po\PORowDTO;
use Zend\Form\Annotation\Object;
use Procure\Application\DTO\Po\PORowDetailsDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRRowSnapshotAssembler
{

    const EXCLUDED_FIELDS = 1;

    const EDITABLE_FIELDS = 2;

    /**
     * generete fields.
     */
    public static function createProperities()
    {
        $entity = new GrRowDetailsSnapshot();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "protected $" . $propertyName . ";";
        }
    }

    public static function createFromDetailsSnapshotCode()
    {
        $itemSnapshot = new GrRowDetailsSnapshot();
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
     * @return NULL|\Procure\Domain\GoodsReceipt\GrRowSnapshot
     */
    public static function createSnapshotFromArray($data)
    {
        if ($data == null)
            return null;

        $snapShot = new GrRowSnapshot();

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
     * @param object $dto
     * @return NULL|\Procure\Domain\GoodsReceipt\GrRowSnapshot
     */
    public static function createSnapshotFromDTO($dto)
    {
        if ($dto == null)
            return null;

        $snapShot = new GrRowSnapshot();

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
     * @param GrRowSnapshot $snapShot
     * @param object $dto
     * @param int $editMode
     * @return NULL|\Procure\Domain\GoodsReceipt\GrRowSnapshot
     */
    public static function updateSnapshotFromDTO(GrRowSnapshot $snapShot, $dto, $editMode = self::EDITABLE_FIELDS)
    {
        if ($dto == null || ! $snapShot instanceof GrRowSnapshot)
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

    /**
     *
     * @param GrRowSnapshot $snapShot
     * @param object $dto
     * @param array $editableProperties
     * @return NULL|\Procure\Domain\GoodsReceipt\GrRowSnapshot
     */
    public static function updateSnapshotFieldsFromDTO(GrRowSnapshot $snapShot, $dto, $editableProperties)
    {
        if ($dto == null || ! $snapShot instanceof GrRowSnapshot || $editableProperties == null)
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

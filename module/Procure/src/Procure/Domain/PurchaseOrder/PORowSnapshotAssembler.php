<?php
namespace Procure\Domain\PurchaseOrder;

use Procure\Application\DTO\Po\PORowDTO;
use Zend\Form\Annotation\Object;
use Procure\Application\DTO\Po\PORowDetailsDTO;
use Procure\Domain\GenericRow;
use Procure\Application\DTO\Po\PORowDTOAssembler;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PORowSnapshotAssembler
{

    const EXCLUDED_FIELDS = 1;

    const EDITABLE_FIELDS = 2;
    
    
    /**
     *
     * @return array;
     */
    public static function findMissingPropertiesOfSnapshot()
    {
        $missingProperties = array();
        $entity = new GenericRow();
        $dto = new PORowSnapshot();
        
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
    
    /**
     *
     * @return array;
     */
    public static function findMissingPropertiesOfEntity()
    {
        $missingProperties = array();
        
        $entityProps = PORowDTOAssembler::createDTOProperities();
        $dto = new GenericRow();
        
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
     * generete fields.
     */
    public static function createProperities()
    {
        $entity = new PORowDetailsSnapshot();
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
        $itemSnapshot = new PORowDetailsSnapshot();
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

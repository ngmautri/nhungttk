<?php
namespace Application\Domain\Attachment;

use Application\DTO\Attachment\AttachmentDTO;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AttachmentSnapshotAssembler
{

    const EXCLUDED_FIELDS = 1;
    const EDITABLE_FIELDS = 2;

    /**
     * generete fields.
     */
    public static function createProperities()
    {
        $entity = new AttachmentDetailsSnapshot();
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
    * @return NULL|\Application\Domain\Attachment\AttachmentSnapshot
    */
    public static function createSnapshotFromArray($data)
    {
        if ($data == null)
            return null;

        $snapShot = new AttachmentSnapshot();

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
     * @param AttachmentDTO $dto
     * @return NULL|\Application\Domain\Attachment\AttachmentSnapshot
     */
    public static function createSnapshotFromDTO(AttachmentDTO $dto)
    {
        if (! $dto instanceof AttachmentDTO)
            return null;

        $snapShot = new AttachmentSnapshot();

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

  
    public static function updateSnapshotFromDTO(AttachmentDTO $dto, AttachmentSnapshot $snapShot, $editMode = self::EDITABLE_FIELDS)
    {
        if (! $dto instanceof AttachmentDTO || ! $snapShot instanceof AttachmentSnapshot)
            return null;

        $reflectionClass = new \ReflectionClass($dto);
        $itemProperites = $reflectionClass->getProperties();

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
            "remarks",
         );

        foreach ($itemProperites as $property) {
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

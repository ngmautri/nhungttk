<?php
namespace Procure\Application\DTO\Pr;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class PrRowDTOAssembler
{

    /**
     * generete DTO File.
     */
    public static function createDTOProperities()
    {
        $entity = new \Application\Entity\NmtProcurePrRow();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();
            print "\n" . "public $" . $propertyName . ";";
        }
    }

    /**
     * generete Mapping.
     */
    public static function createMapping()
    {
        $entity = new \Application\Entity\NmtProcurePrRow();
        $reflectionClass = new \ReflectionClass($entity);
        $itemProperites = $reflectionClass->getProperties();
        foreach ($itemProperites as $property) {
            $property->setAccessible(true);
            $propertyName = $property->getName();

            $v = $property->getValue($entity);

            if (is_object($v)) {
                print "\n" . "\$dto->" . $propertyName . "=\$entity->get" . ucfirst($propertyName) . "();";
            }
        }
    }
}

<?php
namespace Inventory\Domain\Association;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BaseAssociation extends AbstractAssociation
{

    public static function createSnapshotProps()
    {
        $baseClass = "Inventory\Domain\Association\AbstractAssociation";
        $entity = new self();
        $reflectionClass = new \ReflectionClass($entity);

        $props = $reflectionClass->getProperties();

        foreach ($props as $property) {
            // echo $property->class . "\n";
            if ($property->class == $reflectionClass->getName() || $property->class == $baseClass) {
                $property->setAccessible(true);
                $propertyName = $property->getName();
                print "\n" . "public $" . $propertyName . ";";
            }
        }
    }
}
<?php
namespace Inventory\Domain\Warehouse;

use Inventory\Domain\Warehouse\Location\BaseLocation;
use Inventory\Domain\Warehouse\Location\GenericLocation;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class BaseWarehouse extends AbstractWarehouse
{

    // Addtional Attributes
    protected $locations;

    protected $rootLocation;

    protected $returnLocation;

    protected $scrapLocation;

    protected $recycleLocation;

    /**
     *
     * @param GenericLocation $location
     * @throws InvalidArgumentException
     */
    public function addLocation(GenericLocation $location)
    {
        if (! $location instanceof GenericLocation) {
            throw new InvalidArgumentException("Input not invalid! GenericLocation");
        }
        $locations = $this->getLocations();
        $locations[] = $location;
        $this->locations = $locations;
    }

    /**
     *
     * @return mixed
     */
    public function getLocations()
    {
        return $this->locations;
    }

    /**
     *
     * @return NULL|\Inventory\Domain\Warehouse\Location\BaseLocation
     */
    public function getRootLocation()
    {
        if ($this->getLocations() == null) {
            return null;
        }

        foreach ($this->getLocations() as $location) {
            /**
             *
             * @var BaseLocation $location ;
             */
            if ($location->getIsRootLocation()) {
                return $location;
            }
        }

        return null;
    }

    /**
     *
     * @return NULL|\Inventory\Domain\Warehouse\Location\BaseLocation
     */
    public function getReturnLocation()
    {
        if ($this->getLocations() == null) {
            return null;
        }

        foreach ($this->getLocations() as $location) {
            /**
             *
             * @var BaseLocation $location ;
             */
            if ($location->getIsReturnLocation()) {
                return $location;
            }
        }

        return null;
    }

    /**
     *
     * @return NULL|\Inventory\Domain\Warehouse\Location\BaseLocation
     */
    public function getScrapLocation()
    {
        if ($this->getLocations() == null) {
            return null;
        }

        foreach ($this->getLocations() as $location) {
            /**
             *
             * @var BaseLocation $location ;
             */
            if ($location->getIsScrapLocation()) {
                return $location;
            }
        }

        return null;
    }

    /**
     *
     * @return mixed
     */
    public function getRecycleLocation()
    {
        if ($this->getLocations() == null) {
            return null;
        }

        foreach ($this->getLocations() as $location) {
            /**
             *
             * @var BaseLocation $location ;
             */
            if ($location->getIsReturnLocation()) {
                return $location;
            }
        }

        return null;
    }

    public static function createSnapshotProps()
    {
        $baseClass = "Inventory\Domain\Warehouse\BaseWarehouse";
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

    public static function createSnapshotBaseProps()
    {
        $baseClass = "Inventory\Domain\Warehouse\BaseWarehouse";
        $entity = new self();
        $reflectionClass = new \ReflectionClass($entity);

        $props = $reflectionClass->getProperties();

        foreach ($props as $property) {
            // echo $property->class . "\n";
            if ($property->class != $baseClass) {
                $property->setAccessible(true);
                $propertyName = $property->getName();
                print "\n" . "public $" . $propertyName . ";";
            }
        }
    }

    /**
     *
     * @param mixed $locations
     */
    public function setLocations($locations)
    {
        $this->locations = $locations;
    }
}
<?php
namespace Inventory\Domain\Warehouse;

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

    public function addLocation(GenericLocation $location)
    {
        if (! $location instanceof GenericLocation) {
            throw new InvalidArgumentException("Input not invalid! GenericLocation");
        }
        $locations = $this->getLocations();
        $locations[] = $location;
        $this->locations = $locations;
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

    /**
     *
     * @param mixed $rootLocation
     */
    public function setRootLocation($rootLocation)
    {
        $this->rootLocation = $rootLocation;
    }

    /**
     *
     * @param mixed $returnLocation
     */
    public function setReturnLocation($returnLocation)
    {
        $this->returnLocation = $returnLocation;
    }

    /**
     *
     * @param mixed $scrapLocation
     */
    public function setScrapLocation($scrapLocation)
    {
        $this->scrapLocation = $scrapLocation;
    }

    /**
     *
     * @param mixed $recycleLocation
     */
    public function setRecycleLocation($recycleLocation)
    {
        $this->recycleLocation = $recycleLocation;
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
     * @return mixed
     */
    public function getRootLocation()
    {
        return $this->rootLocation;
    }

    /**
     *
     * @return mixed
     */
    public function getReturnLocation()
    {
        return $this->returnLocation;
    }

    /**
     *
     * @return mixed
     */
    public function getScrapLocation()
    {
        return $this->scrapLocation;
    }

    /**
     *
     * @return mixed
     */
    public function getRecycleLocation()
    {
        return $this->recycleLocation;
    }
}
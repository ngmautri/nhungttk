<?php
namespace Inventory\Domain\Warehouse;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseSnapshot extends BaseWarehouseSnapshot
{

    public $locations;

    public $rootLocation;

    public $returnLocation;

    public $scrapLocation;

    public $recycleLocation;

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
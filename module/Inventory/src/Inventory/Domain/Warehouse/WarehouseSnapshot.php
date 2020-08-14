<?php
namespace Inventory\Domain\Warehouse;

use Application\Domain\Shared\Constants;
use Ramsey\Uuid\Uuid;

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

    public function init($createdBy, $createdDate)
    {
        $this->setCreatedOn($createdDate);
        $this->setCreatedBy($createdBy);
        $this->setSysNumber(Constants::SYS_NUMBER_UNASSIGNED);
        $this->setRevisionNo(0);
        $this->setUuid(Uuid::uuid4()->toString());
        $this->setToken($this->getUuid());
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
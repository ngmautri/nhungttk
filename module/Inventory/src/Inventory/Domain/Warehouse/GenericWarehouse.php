<?php
namespace Inventory\Domain\Warehouse;

use Application\Notification;
use Inventory\Domain\Warehouse\Location\GenericLocation;
use Inventory\Domain\Warehouse\Validation\GeneralWarehouseValidation;
use Inventory\Domain\Warehouse\Validation\GeneralLocationValidation;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GenericWarehouse extends AbstractWarehouse
{

    protected $locations;

    protected $rootLocation;

    protected $returnLocation;

    protected $scrapLocation;

    /**
     *
     * @param GenericLocation $location
     */
    public function addLocation(GenericLocation $location)
    {
        $this->locations[] = $location;
    }

    /**
     *
     * @return boolean
     */
    public function isValid()
    {
        $notification = $this->validate();
        return ! $notification->hasErrors();
    }

    /**
     *
     * @param Notification $notification
     */
    public function validate(Notification $notification = null)
    {
        if ($notification == null)
            $notification = new Notification();

        $notification = $this->validateHeader($notification);
        $notification = $this->defaultLocationValidation($notification);

        return $notification;
    }

    /**
     *
     * @param Notification $notification
     */
    public function validateLocation(GenericLocation $location, Notification $notification = null)
    {
        if ($notification == null)
            $notification = new Notification();

            $notification = $this->generalLocationValidation($location, $notification);
        return $notification;
    }

    /**
     *
     * @param Notification $notification
     */
    public function validateHeader(Notification $notification = null)
    {
        if ($notification == null)
            $notification = new Notification();

        $notification = $this->generalHeaderValidation($notification);
        return $notification;
    }

    /**
     *
     * @param Notification $notification
     */
    private function generalHeaderValidation(Notification $notification = null)
    {
        $validator = new GeneralWarehouseValidation();
        return $validator->doValidation($notification, $this);
    }

    /**
     *
     * @param Notification $notification
     */
    private function generalLocationValidation(GenericLocation $location, Notification $notification = null)
    {
        $validator = new GeneralLocationValidation();
        return $validator->doValidation($this, $location, $notification);
    }

    /**
     *
     * @param Notification $notification
     */
    private function defaultLocationValidation(Notification $notification = null)
    {
        if ($notification == null)
            $notification = new Notification();

        if ($this->rootLocation == null || $this->returnLocation == null || $this->scrapLocation == null)
            $notification->addError("Default location (root, return, scrap) not set for this warehouse!");

        return $notification;
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
}
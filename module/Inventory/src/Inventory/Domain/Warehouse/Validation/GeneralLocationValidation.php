<?php
namespace Inventory\Domain\Warehouse\Validation;

use Application\Notification;
use Inventory\Domain\Warehouse\GenericWarehouse;
use Inventory\Domain\Warehouse\Location\GenericLocation;
use Inventory\Domain\Warehouse\Location\DefaultLocation;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GeneralLocationValidation
{

    /**
     *
     * @param GenericWarehouse $wh
     * @param GenericLocation $location
     * @param Notification $notification
     * @return \Application\Notification
     */
    public function doValidation(GenericWarehouse $wh, GenericLocation $location, Notification $notification = null)
    {
        if ($notification == null)
            $notification = new Notification();

        if (! $location instanceof GenericLocation) {
            $notification->addError("location object not found");
        }

        if (! $wh instanceof GenericWarehouse) {
            $notification->addError("Warehouse object not found");
        }

        if ($notification->hasErrors())
            return $notification;

        if ($wh->getSharedSpecificationFactory() == null)
            $notification->addError("Shared Specification not found");

        if ($wh->getCmdRepository() == null)
            $notification->addError("Command Repository for aggregate root not found!");

        if ($wh->getQueryRepository() == null)
            $notification->addError("Query Repository for aggregate root not found!");

        if ($notification->hasErrors())
            return $notification;

        /**
         *
         * @var AbstractSpecification $spec ;
         */

        // company
        $spec = $wh->getSharedSpecificationFactory()->getWarehouseExitsSpecification();

        $subject = array(
            "companyId" => $wh->getCompany(),
            "warehouseId" => $wh->getId()
        );

        if (! $spec->isSatisfiedBy($subject)) {
            $notification->addError("Warehouse not exits. #" . $wh->id);
        }

        // Check Parent Location
        $spec = $wh->getSharedSpecificationFactory()->getWarehouseLocationExitsSpecification();

        $subject = array(
            "warehouseId" => $wh->getId(),
            "locationId" => $location->getParentId()
        );

        if (! $spec->isSatisfiedBy($subject)) {
            $notification->addError("Parent Location not exits. #" . $location->getParentId() . "WH#" . $wh->getId());
        }

        $spec = $wh->getSharedSpecificationFactory()->getNullorBlankSpecification();

        if ($spec->isSatisfiedBy($location->getLocationName())) {
            $notification->addError("location name is null or empty.");
        } else {

            if (preg_match('/[#$%@=+^]/', $location->getLocationName()) == 1) {
                $err = "Location Name contains invalid character (e.g. #,%,&,*)";
                $notification->addError($err);
            }

            $defaultLocation = [
                DefaultLocation::ROOT_LOCATION,
                DefaultLocation::RETURN_LOCATION,
                DefaultLocation::SCRAP_LOCATION
            ];

            if (in_array($location->getLocationName(), $defaultLocation)) {
                $err = sprintf("Location Name is reserved (%s)", $location->getLocationName());
                $notification->addError($err);
            }
        }

        $spec = $wh->getSharedSpecificationFactory()->getUserExitsSpecification();
        $subject = array(
            "companyId" => $wh->getCompany(),
            "userId" => $location->getCreatedBy()
        );

        if (! $spec->isSatisfiedBy($subject)) {
            $notification->addError("User is not identified for this transaction. #" . $location->getCreatedBy());
        }

      
        return $notification;
    }
}
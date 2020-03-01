<?php
namespace Inventory\Domain\Warehouse\Transaction\Validation;

use Application\Notification;
use Inventory\Domain\Warehouse\GenericWarehouse;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GeneralRowValidation
{

    public function doValidation(Notification $notification = null, GenericWarehouse $wh)
    {
        if ($notification == null)
            $notification = new Notification();

        if ($row == null)
            return $notification;

        /**
         *
         * @var AbstractSpecificationForCompany $spec ;
         */
        if ($this->sharedSpecificationFactory == null) {
            $notification->addError("Validators is not found");
            return $notification;
        }

        if ($row->getMvUuid() !== $this->uuid) {
            $notification->addError("transaction id not match");
            return $notification;
        }

        // do verification now

        // check item exits
        $spec = $this->sharedSpecificationFactory->getItemExitsSpecification();
        $spec->setCompanyId($this->company);

        $subject = array(
            "companyId" => $this->company,
            "itemId" => $row->getItem()
        );

        if (! $spec->isSatisfiedBy($subject))
            $notification->addError("Item not exits in the company #" . $this->company);

        // Check quantity.
        $spec = $this->sharedSpecificationFactory->getPositiveNumberSpecification();
        if (! $spec->isSatisfiedBy($row->getDocQuantity()))
            $notification->addError("Quantity is not valid! " . $row->getDocQuantity());
                        
        return $notification;
    }
}
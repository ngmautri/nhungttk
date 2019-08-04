<?php
namespace Inventory\Domain\Warehouse\Location;

use Application\Notification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GenericLocation extends AbstractLocation
{

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

        $notification = $this->generalValidation($notification);

        return $notification;
    }

    /**
     *
     * @param Notification $notification
     */
    protected function generalValidation(Notification $notification = null)
    {
        if ($notification == null)
            $notification = new Notification();

        if ($this->sharedSpecificationFactory == null)
            $notification->addError("Shared Specification not found");

        if ($this->cmdRepository == null)
            $notification->addError("Command Repository for aggregate root not found!");

        if ($this->queryRepository == null)
            $notification->addError("Query Repository for aggregate root not found!");

        if ($notification->hasErrors())
            return $notification;

        /**
         *
         * @var AbstractSpecification $spec ;
         */

        // company
        $spec = $this->sharedSpecificationFactory->getCompanyExitsSpecification();
        if (! $spec->isSatisfiedBy($this->company)) {
            $notification->addError("Company not exits. #" . $this->company);
        }

        $spec = $this->sharedSpecificationFactory->getNullorBlankSpecification();

        if ($spec->isSatisfiedBy($this->getWhName())) {
            $notification->addError("WH name is null or empty.");
        } else {

            if (preg_match('/[#$%@=+^]/', $this->getWhName()) == 1) {
                $err = "Warehouse Name contains invalid character (e.g. #,%,&,*)";
                $notification->addError($err);
            }
        }

        if ($spec->isSatisfiedBy($this->getWhCode())) {
            $notification->addError("WH code is null or empty.");
        } else {

            if (preg_match('/[#$%@=+^]/', $this->getWhCode()) == 1) {
                $err = "Warehouse Code contains invalid character (e.g. #,%,&,*)";
                $notification->addError($err);
            }

            $spec = $this->sharedSpecificationFactory->getWarehouseCodeExitsSpecification();
            $subject = array(
                "companyId" => $this->company,
                "whCode" => $this->whCode
            );

            if ($spec->isSatisfiedBy($subject)) {
                $notification->addError("Warehouse Code exits! #" . $this->whCode);
            }
        }

        $spec = $this->sharedSpecificationFactory->getUserExitsSpecification();
        $subject = array(
            "companyId" => $this->company,
            "userId" => $this->createdBy
        );

        if (! $spec->isSatisfiedBy($subject)) {
            $notification->addError("User is not identified for this transaction. #" . $this->createdBy);
        }

        // Controller
        if ($this->whController > 0) {

            $subject = array(
                "companyId" => $this->company,
                "userId" => $this->whController
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $notification->addError("Controller can not be identified. #" . $this->whController);
            }
        }

        return $notification;
    }
}
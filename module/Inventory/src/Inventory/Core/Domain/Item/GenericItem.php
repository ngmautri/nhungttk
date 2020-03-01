<?php
namespace Inventory\Domain\Item;

use Application\Notification;
use Application\Domain\Shared\Specification\AbstractSpecificationFactory;
use Application\Domain\Shared\Specification\AbstractSpecification;
use Application\Domain\Shared\Specification\AbstractSpecificationForCompany;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class GenericItem extends AbstractItem
{

    /**
     *
     * @var AbstractSpecificationFactory
     */
    protected $sharedSpecificationFactory;

    public function __construct()
    {
        $this->specifyItem();
    }

    /**
     *
     * @param Notification $notification
     */
    abstract public function specificValidation(Notification $notification = null);

    abstract public function specifyItem();

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

        // overview some property
        $this->specifyItem();

        $notification = $this->generalValidation($notification);

        $specificValidationResult = $this->specificValidation($notification);

        if ($specificValidationResult !== null)
            $notification = $specificValidationResult;

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

        if ($notification->hasErrors())
            return $notification;

        /**
         *
         * @var AbstractSpecification $spec
         */

        // company
        $spec = $this->sharedSpecificationFactory->getCompanyExitsSpecification();
        if (! $spec->isSatisfiedBy($this->company)) {
            $notification->addError("Company not exits. #" . $this->company);
        }

        $spec = $this->sharedSpecificationFactory->getUserExitsSpecification();
        $subject = array(
            "companyId" => $this->company,
            "userId" => $this->createdBy
        );

        if (! $spec->isSatisfiedBy($subject)) {
            $notification->addError("User is not identified for this transaction. #" . $this->createdBy);
        }

        $spec = $this->sharedSpecificationFactory->getNullorBlankSpecification();
        if ($spec->isSatisfiedBy($this->getItemName())) {
            $notification->addError("Item name is null or empty. It is required for any item.");
        } else {

           /*  if (preg_match('/[#$%@=+^]/', $this->getItemName()) == 1) {
                $err = "Item name contains invalid character (e.g. #,%,&,*)";
                $notification->addError($err);
            } */
            
            if (preg_match('/[$^]/', $this->getItemName()) == 1) {
                $err = "Item name contains invalid character (e.g. $)";
                $notification->addError($err);
            }
        }

        return $notification;
    }

    /**
     *
     * @return \Application\Domain\Shared\Specification\AbstractSpecificationFactory
     */
    public function getSharedSpecificationFactory()
    {
        return $this->sharedSpecificationFactory;
    }

    /**
     *
     * @param \Application\Domain\Shared\Specification\AbstractSpecificationFactory $sharedSpecificationFactory
     */
    public function setSharedSpecificationFactory($sharedSpecificationFactory)
    {
        $this->sharedSpecificationFactory = $sharedSpecificationFactory;
    }
}
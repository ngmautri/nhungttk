<?php
namespace Inventory\Domain\Warehouse\Validator;

use Application\Notification;
use Application\Domain\Shared\Specification\AbstractSpecification;
use Inventory\Domain\Validator\Warehouse\WarehouseValidatorInterface;
use Inventory\Domain\Warehouse\AbstractWarehouse;
use Inventory\Domain\Warehouse\GenericWarehouse;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultWarehouseValidator implements WarehouseValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Validator\Warehouse\WarehouseValidatorInterface::validate()
     */
    public function validate(AbstractWarehouse $rootEntity)
    {}

    public function doValidation(Notification $notification = null, GenericWarehouse $wh)
    {
        if ($notification == null)
            $notification = new Notification();

        if (! $wh instanceof GenericWarehouse) {
            $notification->addError("Warehouse object not found");
            return $notification;
        }

        if ($wh->getSharedSpecificationFactory() == null)
            $notification->addError("Shared Specification not found");

        if ($wh->getCmdRepository() == null)
            $notification->addError("Cmd Repository for aggregate root not found!");

        if ($wh->getQueryRepository() == null)
            $notification->addError("Query Repository for aggregate root not found!");

        if ($notification->hasErrors())
            return $notification;

        /**
         *
         * @var AbstractSpecification $spec ;
         */

        // company
        $spec = $wh->getSharedSpecificationFactory()->getCompanyExitsSpecification();
        if (! $spec->isSatisfiedBy($wh->getCompany())) {
            $notification->addError("Company not exits. #" . $wh->getCompany());
        }

        $spec = $wh->getSharedSpecificationFactory()->getNullorBlankSpecification();

        if ($spec->isSatisfiedBy($wh->getWhName())) {
            $notification->addError("WH name is null or empty.");
        } else {

            if (preg_match('/[#$%@=+^]/', $wh->getWhName()) == 1) {
                $err = "Warehouse Name contains invalid character (e.g. #,%,&,*)";
                $notification->addError($err);
            }
        }

        if ($spec->isSatisfiedBy($wh->getWhCode())) {
            $notification->addError("WH code is null or empty.");
        } else {

            if (preg_match('/[#$%@=+^]/', $wh->getWhCode()) == 1) {
                $err = "Warehouse Code contains invalid character (e.g. #,%,&,*)";
                $notification->addError($err);
            }

            $spec = $wh->getSharedSpecificationFactory()->getWarehouseCodeExitsSpecification();
            $subject = array(
                "companyId" => $wh->getCompany(),
                "whCode" => $wh->getWhCode(),
                "warehouseId" => $wh->getId() // in case of warehouse update.
            );

            if ($spec->isSatisfiedBy($subject)) {
                $notification->addError("Warehouse Code exits! #" . $wh->getWhCode());
            }
        }

        $spec = $wh->getSharedSpecificationFactory()->getUserExitsSpecification();
        $subject = array(
            "companyId" => $wh->getCompany(),
            "userId" => $wh->getCreatedBy()
        );

        if (! $spec->isSatisfiedBy($subject)) {
            $notification->addError("User is not identified for this transaction. #" . $wh->getCreatedBy());
        }

        // Controller
        if ($wh->getWhController() > 0) {

            $subject = array(
                "companyId" => $wh->getCompany(),
                "userId" => $wh->getWhController()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $notification->addError("Controller can not be identified. #" . $wh->getWhController());
            }
        }

        if (! $wh->getWhEmail() == null) {
            $spec = $wh->getSharedSpecificationFactory()->getEmailSpecification();
            if (! $spec->isSatisfiedBy($wh->getWhEmail())) {
                $notification->addError("Email invalid " . $wh->getWhEmail());
            }
        }

        return $notification;
    }
    
  

}
<?php
namespace Inventory\Domain\Warehouse\Transaction\Validation;

use Application\Notification;
use Inventory\Domain\Warehouse\GenericWarehouse;
use Inventory\Domain\Warehouse\Transaction\TransactionType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GeneralHeaderValidation
{

    public function doValidation(Notification $notification = null, GenericWarehouse $wh)
    {
        if ($notification == null)
            $notification = new Notification();

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        if ($this->domainSpecificationFactory == null || $this->sharedSpecificationFactory == null)
            $notification->addError("Validators is not found");

        if ($notification->hasErrors())
            return $notification;

        // do verification now

        // company
        $spec = $this->sharedSpecificationFactory->getCompanyExitsSpecification();
        if (! $spec->isSatisfiedBy($this->company)) {
            $notification->addError("Company not exits. #" . $this->company);
        }

        // check movement type
        if ($this->sharedSpecificationFactory->getNullorBlankSpecification()->isSatisfiedBy($this->movementType)) {
            $notification->addError("Transaction Type is not set.");
        } else {
            $supportedType = TransactionType::getSupportedTransaction();
            if (! in_array($this->movementType, $supportedType)) {
                $notification->addError("Transaction Type is not supported");
            }

            // check if transactition type is consistent - when change header

            if (count($this->transactionRows) > 0) {
                foreach ($this->transactionRows as $row) {
                    /**
                     *
                     * @var TransactionRow $row ;
                     */
                    if ($row->getDocType() !== $this->movementType) {
                        $notification->addError("Transaction Type is inconsistent, Because at least one row has different type");
                        break;
                    }
                }
            }
        }

        if (! $this->sharedSpecificationFactory->getDateSpecification()->isSatisfiedBy($this->movementDate)) {
            $notification->addError("Transaction date is not correct or empty");
        } else {

            /**
             *
             * @var CanPostOnDateSpecification $spec ;
             */
            $spec1 = $this->sharedSpecificationFactory->getCanPostOnDateSpecification();
            $subject = array(
                "companyId" => $this->company,
                "movementDate" => $this->movementDate
            );

            if (! $spec1->isSatisfiedBy($subject)) {
                $notification->addError("Can not post on this date. Period is not created or closed." . $this->movementDate);
            }
        }

        if (! $this->sharedSpecificationFactory->getCurrencyExitsSpecification()->isSatisfiedBy($this->docCurrency))
            $notification->addError("Currency is empty or invalid");

        // check warehouse currency
        if ($this->warehouse == null) {
            $notification->addError("Source warehouse is not set");
        } else {

            $spec1 = $this->sharedSpecificationFactory->getWarehouseACLSpecification();
            $subject = array(
                "companyId" => $this->company,
                "warehouseId" => $this->warehouse,
                "userId" => $this->createdBy
            );
            if (! $spec1->isSatisfiedBy($subject))
                $notification->addError(sprintf("Warehouse not found or insuffient authority for this Warehouse!C#%s, WH#%s, U#%s", $this->company, $this->warehouse, $this->createdBy));
        }

        // check local currency
        if ($this->localCurrency == null) {
            $notification->addError("Local currency is not set");
        } else {
            $spec = $this->sharedSpecificationFactory->getCurrencyExitsSpecification();
            if (! $spec->isSatisfiedBy($this->localCurrency))
                $notification->addError("Local currency not exits..." . $this->localCurrency);
        }
                        
         return $notification;
    }
}
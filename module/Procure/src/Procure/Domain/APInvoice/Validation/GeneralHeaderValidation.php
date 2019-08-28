<?php
namespace Procure\Domain\APInvoice\Validation;

use Application\Notification;
use Inventory\Domain\Service\TransactionSpecificationService;
use Inventory\Domain\Warehouse\Transaction\TransactionType;
use Procure\Domain\Service\APInvoiceSpecificationService;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GeneralHeaderValidation
{

    /**
     *
     * @param APInvoiceSpecificationService $specificationService
     * @param Notification $notification
     * @return \Application\Notification
     */
    public function doValidation(APInvoiceSpecificationService $specificationService, Notification $notification)
    {
        /**
         *
         * @var TransactionSpecificationService $specificationService ;
         */
        if ($specificationService == null) {
            $notification->addError("Specification factory is not found");
        }

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        if ($specificationService->getDomainSpecificationFactory() == null || $specificationService->getSharedSpecificationFactory() == null)
            $notification->addError("Validators is not found");

        if ($notification->hasErrors())
            return $notification;

        // do verification now

        // company
        $spec = $specificationService->getSharedSpecificationFactory()->getCompanyExitsSpecification();
        if (! $spec->isSatisfiedBy($this->company)) {
            $notification->addError("Company not exits. #" . $this->company);
        }

        // check movement type
        if ($specificationService->getSharedSpecificationFactory()
            ->getNullorBlankSpecification()
            ->isSatisfiedBy($this->movementType)) {
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
                    if ($row->getTransactionType() !== $this->movementType) {
                        $notification->addError("Transaction Type is inconsistent, because at least one row has different type");
                        break;
                    }
                }
            }
        }

        if (! $specificationService->getSharedSpecificationFactory()
            ->getDateSpecification()
            ->isSatisfiedBy($this->movementDate)) {
            $notification->addError("Transaction date is not correct or empty");
        } else {

            /**
             *
             * @var CanPostOnDateSpecification $spec ;
             */
            $spec1 = $specificationService->getSharedSpecificationFactory()->getCanPostOnDateSpecification();
            $subject = array(
                "companyId" => $this->company,
                "movementDate" => $this->movementDate
            );

            if (! $spec1->isSatisfiedBy($subject)) {
                $notification->addError("Can not post on this date. Period is not created or closed." . $this->movementDate);
            }
        }

        if (! $specificationService->getSharedSpecificationFactory()
            ->getCurrencyExitsSpecification()
            ->isSatisfiedBy($this->docCurrency))
            $notification->addError("Currency is empty or invalid");

        // check warehouse currency
        if ($this->warehouse == null) {
            $notification->addError("Source warehouse is not set");
        } else {

            $spec1 = $specificationService->getSharedSpecificationFactory()->getWarehouseACLSpecification();
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
            $spec = $specificationService->getSharedSpecificationFactory()->getCurrencyExitsSpecification();
            if (! $spec->isSatisfiedBy($this->localCurrency))
                $notification->addError("Local currency not exits..." . $this->localCurrency);
        }

        return $notification;
    }
}
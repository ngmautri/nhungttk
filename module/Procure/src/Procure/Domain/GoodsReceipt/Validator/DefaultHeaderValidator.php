<?php
namespace Procure\Domain\GoodsReceipt\Validator;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Procure\Domain\Exception\Gr\GrCreateException;
use Procure\Domain\Exception\Gr\GrInvalidArgumentException;
use Procure\Domain\GoodsReceipt\GenericGR;
use Application\Application\Specification\Zend\CanPostOnDateSpecification;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultHeaderValidator extends AbstractValidator implements HeaderValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\Validator\HeaderValidatorInterface::validate()
     */
    public function validate($rootEntity)
    {
        if (! $rootEntity instanceof GenericGR) {
            throw new GrInvalidArgumentException('Root entity not given!');
        }

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        try {

            // ==== CK COMPANY =======
            $spec = $this->sharedSpecificationFactory->getCompanyExitsSpecification();
            if (! $spec->isSatisfiedBy($rootEntity->getCompany())) {
                $rootEntity->addError("Company not exits. #" . $rootEntity->getCompany());
            }

            // ===== CK VENDOR =======
            if ($rootEntity->getVendor() == null) {
                $rootEntity->addError("Vendor is not set");
            } else {
                $spec = $this->sharedSpecificationFactory->getVendorExitsSpecification();
                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "vendorId" => $rootEntity->getVendor()
                );
                if (! $spec->isSatisfiedBy($subject))
                    $rootEntity->addError(sprintf("Vendor not found !C#%s, WH#%s", $rootEntity->getCompany(), $rootEntity->getVendor()));
            }

            // ==== POSTING DATE =======
            $spec = $this->sharedSpecificationFactory->getDateSpecification();
            if (! $spec->isSatisfiedBy($rootEntity->getPostingDate())) {
                $rootEntity->addError("Posting date is not correct or empty");
            } else {
                /**
                 *
                 * @var CanPostOnDateSpecification $spec ;
                 */
                $spec1 = $this->getSharedSpecificationFactory()->getCanPostOnDateSpecification();
                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "movementDate" => $rootEntity->getPostingDate()
                );

                if (! $spec1->isSatisfiedBy($subject)) {
                    $rootEntity->addError("Can not post on this date. Period is not created or closed." . $rootEntity->getPostingDate());
                }
            }

            // ===== WAREHOUSE =======
            if ($rootEntity->getWarehouse() == null) {
                $rootEntity->addError("Source warehouse is not set");
            } else {

                $spec1 = $this->getSharedSpecificationFactory()->getWarehouseACLSpecification();
                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "warehouseId" => $rootEntity->getWarehouse(),
                    "userId" => $rootEntity->getCreatedBy()
                );
                if (! $spec1->isSatisfiedBy($subject))
                    $rootEntity->addError(sprintf("Warehouse not found or insuffient authority for this Warehouse!C#%s, WH#%s, U#%s", $rootEntity->getCompany(), $rootEntity->getWarehouse(), $rootEntity->getCreatedBy()));
            }

            // ===== DOC CURRENCY =======
            if (! $this->sharedSpecificationFactory->getCurrencyExitsSpecification()->isSatisfiedBy($rootEntity->getDocCurrency())) {
                $rootEntity->addError(sprintf("Doc Currency is empty or invalid! %s", $rootEntity->getDocCurrency()));
            }

            // ===== LOCAL CURRENCY =======
            if ($rootEntity->getLocalCurrency() == null) {
                $rootEntity->addError("Local currency is not set");
            } else {
                $spec = $this->sharedSpecificationFactory->getCurrencyExitsSpecification();
                if (! $spec->isSatisfiedBy($rootEntity->getLocalCurrency()))
                    $rootEntity->addError("Local currency not exits..." . $rootEntity->getLocalCurrency());
            }

            // ===== USER ID =======
            $spec = $this->sharedSpecificationFactory->getUserExitsSpecification();
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "userId" => $rootEntity->getCreatedBy()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $rootEntity->addError("User is not identified for this transaction. #" . $rootEntity->getCreatedBy());
            }

            if ($rootEntity->getLastchangeBy() !== null) {
                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "userId" => $rootEntity->getLastchangeBy()
                );
                if (! $spec->isSatisfiedBy($subject)) {
                    $rootEntity->addError("User is not identified for this transaction. #" . $rootEntity->getLastchangeBy());
                }
            }
            
            
            
        } catch (GrCreateException $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}


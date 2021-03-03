<?php
namespace Procure\Domain\PurchaseRequest\Validator\Header;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Application\Domain\Util\Translator;
use Procure\Domain\AbstractDoc;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseRequest\GenericPR;
use Procure\Domain\Validator\AbstractValidator;
use Procure\Domain\Validator\HeaderValidatorInterface;

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
     * @see \Procure\Domain\Validator\HeaderValidatorInterface::validate()
     */
    public function validate(AbstractDoc $rootEntity)
    {
        if (! $rootEntity instanceof GenericPR) {
            throw new InvalidArgumentException(\sprintf('Root entity not given!%s', __METHOD__));
        }

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        try {

            // ==== CK COMPANY =======
            $spec = $this->sharedSpecificationFactory->getCompanyExitsSpecification();
            if (! $spec->isSatisfiedBy($rootEntity->getCompany())) {
                $rootEntity->addError(Translator::translate("Company not exits. #" . $rootEntity->getCompany()));
            }

            $spec = $this->sharedSpecificationFactory->getNullorBlankSpecification();

            // ==== PR NUMBER =======
            if ($spec->isSatisfiedBy($rootEntity->getPrNumber())) {
                $rootEntity->addError(Translator::translate(\sprintf("PR number is required!%s", $rootEntity->getPrNumber())));
            }

            $spec = $this->sharedSpecificationFactory->getDateSpecification();
            if (! $spec->isSatisfiedBy($rootEntity->getSubmittedOn())) {
                $rootEntity->addError(\sprintf("PR Date is not correct or empty. %s", $rootEntity->getSubmittedOn()));
            }

            // ===== Department ID =======
            $spec = $this->sharedSpecificationFactory->getDepartmentSpecification();
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "departmentId" => $rootEntity->getDepartment()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $rootEntity->addError("Department is not correct or empty");
            }

            // ===== Warehouse ID =======
            $spec = $this->getSharedSpecificationFactory()->getWarehouseExitsSpecification();
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "warehouseId" => $rootEntity->getWarehouse()
            );
            if (! $spec->isSatisfiedBy($subject)) {
                $rootEntity->addError(sprintf("Warehouse is required! %s", $rootEntity->getWarehouse()));
            }

            // ===== USER ID =======
            $spec = $this->sharedSpecificationFactory->getUserExitsSpecification();
            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "userId" => $rootEntity->getCreatedBy()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $rootEntity->addError(Translator::translate("User is not identified for this transaction. #" . $rootEntity->getCreatedBy()));
            }

            if ($rootEntity->getLastchangeBy() !== null) {
                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "userId" => $rootEntity->getLastchangeBy()
                );
                if (! $spec->isSatisfiedBy($subject)) {
                    $rootEntity->addError(Translator::translate("User is not identified for this transaction. #" . $rootEntity->getLastchangeBy()));
                }
            }
        } catch (\Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}


<?php
namespace Application\Domain\Company\AccountChart\Validator;

use Application\Domain\Company\AccountChart\BaseChart;
use Application\Domain\Company\AccountChart\Validator\Contracts\ChartValidatorInterface;
use Application\Domain\Company\Validator\Contracts\AbstractValidator;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ChartDefaultValidator extends AbstractValidator implements ChartValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\AccountChart\Validator\Contracts\ChartValidatorInterface::validate()
     */
    public function validate(BaseChart $rootEntity)
    {
        if (! $rootEntity instanceof BaseChart) {
            $rootEntity->addError("BaseChart object not found");
            return;
        }

        try {
            // company
            $spec = $this->getSharedSpecificationFactory()->getCompanyExitsSpecification();
            if (! $spec->isSatisfiedBy($rootEntity->getCompany())) {
                $rootEntity->addError("Company not exits. #" . $rootEntity->getCompany());
            }

            $spec = $this->getSharedSpecificationFactory()->getNullorBlankSpecification();

            if ($spec->isSatisfiedBy($rootEntity->getCoaCode())) {
                $rootEntity->addError("Chart code is null or empty.");
            } else {

                if (preg_match('/[#$%@=+^]/', $rootEntity->getCoaCode()) == 1) {
                    $err = "Chart code contains invalid character (e.g. #,%,&,*)";
                    $rootEntity->addError($err);
                }
            }

            if ($spec->isSatisfiedBy($rootEntity->getCoaName())) {
                $rootEntity->addError("Chart name is null or empty.");
            } else {

                if (preg_match('/[#$%@=+^]/', $rootEntity->getCoaName()) == 1) {
                    $err = "Chart name contains invalid character (e.g. #,%,&,*)";
                    $rootEntity->addError($err);
                }
            }

            // User
            $spec = $this->getSharedSpecificationFactory()->getUserExitsSpecification();

            // Created by
            if ($rootEntity->getCreatedBy() > 0) {

                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "userId" => $rootEntity->getCreatedBy()
                );

                if (! $spec->isSatisfiedBy($subject)) {
                    $rootEntity->addError(\sprintf("User can not be identified. #%s, %s ", $rootEntity->getCreatedBy(), __CLASS__));
                }
            }

            // Last Change by
            if ($rootEntity->getLastChangeBy() > 0) {

                $subject = array(
                    "companyId" => $rootEntity->getCompany(),
                    "userId" => $rootEntity->getLastChangeBy()
                );

                if (! $spec->isSatisfiedBy($subject)) {
                    $rootEntity->addError("User can not be identified. #" . $rootEntity->getLastChangeBy());
                }
            }
        } catch (Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}
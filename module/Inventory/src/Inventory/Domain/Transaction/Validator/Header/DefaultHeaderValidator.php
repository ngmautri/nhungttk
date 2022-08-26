<?php
namespace Inventory\Domain\Transaction\Validator\Header;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Inventory\Domain\Transaction\GenericTrx;
use Inventory\Domain\Transaction\Contracts\TrxType;
use Inventory\Domain\Transaction\Validator\Contracts\AbstractValidator;
use Inventory\Domain\Transaction\Validator\Contracts\HeaderValidatorInterface;
use Procure\Domain\AbstractDoc;
use InvalidArgumentException;
use RuntimeException;

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
        if (! $rootEntity instanceof GenericTrx) {
            throw new InvalidArgumentException('GenericTrx entity not given!');
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

            $spec = $this->sharedSpecificationFactory->getNullorBlankSpecification();
            if ($spec->isSatisfiedBy($rootEntity->getMovementType())) {
                $rootEntity->addError("Goods movement type is not set" . __FUNCTION__);
            } else {
                $supportedType = TrxType::getSupportedTransaction();
                if (! in_array($rootEntity->getMovementType(), $supportedType)) {
                    $rootEntity->addError("Goods movement Type is not supported" . __FUNCTION__);
                }
            }

            // ===== USER ID =======
            $spec = $this->sharedSpecificationFactory->getCompanyUserExSpecification();
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
        } catch (RuntimeException $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}


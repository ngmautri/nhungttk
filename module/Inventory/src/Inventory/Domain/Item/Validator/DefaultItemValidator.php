<?php
namespace Inventory\Domain\Item\Validator;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Inventory\Domain\Item\AbstractItem;
use Inventory\Domain\Validator\Item\AbstractItemValidator;
use Inventory\Domain\Validator\Item\ItemValidatorInterface;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultItemValidator extends AbstractItemValidator implements ItemValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Validator\Item\ItemValidatorInterface::validate()
     */
    public function validate(AbstractItem $rootEntity)
    {
        if (! $rootEntity instanceof AbstractItem) {
            throw new InvalidArgumentException('Root entity not given!');
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

            $spec = $this->sharedSpecificationFactory->getNullorBlankSpecification();
            if ($spec->isSatisfiedBy($rootEntity->getItemName())) {
                $rootEntity->addError("Item name is null or empty. It is required for any item.");
            } else {

                if (preg_match('/[$^]/', $rootEntity->getItemName()) == 1) {
                    $err = "Item name contains invalid character (e.g. $)";
                    $rootEntity->addError($err);
                }
            }
        } catch (\Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}


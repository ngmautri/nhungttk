<?php
namespace Inventory\Domain\Association\Validator;

use Application\Domain\Shared\Specification\AbstractSpecification;
use Inventory\Domain\Association\AbstractAssociation;
use Inventory\Domain\Validator\Association\AbstractAssociationValidator;
use Inventory\Domain\Validator\Association\AssociationValidatorInterface;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DefaultValidator extends AbstractAssociationValidator implements AssociationValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Validator\Item\ItemValidatorInterface::validate()
     */
    public function validate(AbstractAssociation $rootEntity)
    {
        if (! $rootEntity instanceof AbstractAssociation) {
            throw new InvalidArgumentException('Root entity not given!');
        }

        /**
         *
         * @var AbstractSpecification $spec ;
         */
        try {

            // ==== CK COMPANY =======

            $spec = $this->sharedSpecificationFactory->getAssociationExitsSpecification();

            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "associationId" => $rootEntity->getAssociation()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $rootEntity->addError(sprintf("Item association #%s not exits in the company #%s", $rootEntity->getAssociation(), $rootEntity->getCompany()));
            }

            if ($rootEntity->getMainItem() == $rootEntity->getRelatedItem() && $rootEntity->getMainItem() !== null) {
                $rootEntity->addError(sprintf("Item are the same %s-%s", $rootEntity->getMainItem(), $rootEntity->getRelatedItem()));
            }

            $spec = $this->sharedSpecificationFactory->getItemExitsSpecification();

            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "itemId" => $rootEntity->getMainItem()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $rootEntity->addError(sprintf("Main Item #%s not exits in the company #%s", $rootEntity->getMainItem(), $rootEntity->getCompany()));
            }

            $subject = array(
                "companyId" => $rootEntity->getCompany(),
                "itemId" => $rootEntity->getRelatedItem()
            );

            if (! $spec->isSatisfiedBy($subject)) {
                $rootEntity->addError(sprintf("Related Item #%s not exits in the company #%s", $rootEntity->getRelatedItem(), $rootEntity->getCompany()));
            }

            $spec = $this->sharedSpecificationFactory->getAssociationItemExitsSpecification();

            $subject = array(
                "associationId" => $rootEntity->getAssociation(),
                "mainItemId" => $rootEntity->getMainItem(),
                "relatedItemId" => $rootEntity->getRelatedItem()
            );

            if ($spec->isSatisfiedBy($subject)) {
                $rootEntity->addError(sprintf("Relation exits %s-%s-%s", $rootEntity->getMainItem(), $rootEntity->getAssociation(), $rootEntity->getRelatedItem()));
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
        } catch (\Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}


<?php
namespace Application\Domain\Company\ItemAssociation\Validator;

use Application\Domain\Company\ItemAssociation\BaseAssociation;
use Application\Domain\Company\ItemAssociation\Validator\Contracts\ItemAssociationValidatorInterface;
use Application\Domain\Company\ItemAttribute\BaseAttributeGroup;
use Application\Domain\Company\Validator\Contracts\AbstractValidator;
use Exception;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AssociatioDefaultValidator extends AbstractValidator implements ItemAssociationValidatorInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\ItemAssociation\Validator\Contracts\ItemAssociationValidatorInterface::validate()
     */
    public function validate(BaseAssociation $rootEntity)
    {
        if (! $rootEntity instanceof BaseAttributeGroup) {
            $rootEntity->addError("BaseAttributeGroup object not found");
            return;
        }

        try {} catch (Exception $e) {
            $rootEntity->addError($e->getMessage());
        }
    }
}
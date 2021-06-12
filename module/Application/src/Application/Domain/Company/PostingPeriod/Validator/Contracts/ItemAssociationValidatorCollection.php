<?php
namespace Application\Domain\Company\ItemAssociation\Validator\Contracts;

use Application\Domain\Company\ItemAssociation\BaseAssociation;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ItemAssociationValidatorCollection implements ItemAssociationValidatorInterface

{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(ItemAssociationValidatorInterface $validator)
    {
        if (! $validator instanceof ItemAssociationValidatorInterface) {
            throw new InvalidArgumentException("ItemAssociationValidatorInterface Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\ItemAssociation\Validator\Contracts\ItemAssociationValidatorInterface::validate()
     */
    public function validate(BaseAssociation $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("BaseAssociation is required! but no is given.");
        }

        foreach ($this->validators as $validator) {
            $validator->validate($rootEntity);
        }
    }
}


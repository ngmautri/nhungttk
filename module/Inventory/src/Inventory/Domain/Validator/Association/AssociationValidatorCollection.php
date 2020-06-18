<?php
namespace Inventory\Domain\Validator\Association;

use Inventory\Domain\Association\AbstractAssociation;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AssociationValidatorCollection implements AssociationValidatorInterface
{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(AssociationValidatorInterface $validator)
    {
        if (! $validator instanceof AssociationValidatorInterface) {
            throw new InvalidArgumentException("AssociationValidatorInterface is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Validator\Item\ItemValidatorInterface::validate()
     */
    public function validate(AbstractAssociation $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("AssociationValidatorInterface is required! but no is given.");
        }

        foreach ($this->validators as $validator) {
            /**
             *
             * @var AssociationValidatorInterface $validator ;
             */
            $validator->validate($rootEntity);
        }
    }
}


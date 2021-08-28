<?php
namespace Inventory\Domain\Item\Component\Validator\Contracts;

use Inventory\Domain\Item\CompositeItem;
use Inventory\Domain\Item\Component\BaseComponent;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ComponentValidatorCollection implements ComponentValidatorInterface

{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(ComponentValidatorInterface $validator)
    {
        if (! $validator instanceof ComponentValidatorInterface) {
            throw new InvalidArgumentException("ComponentValidatorInterface is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Component\Validator\Contracts\ComponentValidatorInterface::validate()
     */
    public function validate(CompositeItem $rootEntity, BaseComponent $localEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("ComponentValidatorInterface is required! but none is given.");
        }

        foreach ($this->validators as $validator) {
            $validator->validate($rootEntity, $localEntity);
        }
    }
}


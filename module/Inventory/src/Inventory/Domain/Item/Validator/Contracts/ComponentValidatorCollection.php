<?php
namespace Inventory\Domain\Component\Validator\Contracts;

use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\Component\BaseComponent;
use Inventory\Domain\Warehouse\Validator\Contracts\LocationValidatorInterface;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ComponentValidatorCollection implements ComponentValidatorInterface
{

    private $validators;

    /**
     *
     * @param LocationValidatorInterface $validator
     * @throws InvalidArgumentException
     */
    public function add(ComponentValidatorInterface $validator)
    {
        if (! $validator instanceof ComponentValidatorInterface) {
            throw new InvalidArgumentException(sprintf("Row Validator is required! %s ", get_class($this)));
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Component\Validator\Contracts\ComponentValidatorInterface::validate()
     */
    public function validate(GenericItem $rootEntity, BaseComponent $localEntity)
    {
        if (! $rootEntity instanceof GenericItem) {
            throw new InvalidArgumentException("Document type is not valid.");
        }

        if (! $localEntity instanceof BaseComponent) {
            throw new InvalidArgumentException("Document row type is not valid.");
        }

        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("BaseComponent Validator is required! but no is given.");
        }

        foreach ($this->validators as $validator) {

            /**
             *
             * @var ComponentValidatorInterface $validator ;
             */
            $validator->validate($rootEntity, $localEntity);
        }
    }
}


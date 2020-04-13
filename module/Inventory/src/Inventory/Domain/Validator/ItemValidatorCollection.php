<?php
namespace Inventory\Domain\Validator;

use Inventory\Domain\Item\AbstractItem;
use Procure\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class ItemValidatorCollection implements ItemValidatorInterface
{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(ItemValidatorInterface $validator)
    {
        if (! $validator instanceof ItemValidatorInterface) {
            throw new InvalidArgumentException("Item Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Validator\ItemValidatorInterface::validate()
     */
    public function validate(AbstractItem $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("Item Validator is required! but no is given.");
        }

        foreach ($this->validators as $validator) {
            /**
             *
             * @var ItemValidatorInterface $validator ;
             */
            $validator->validate($rootEntity);
        }
    }
}


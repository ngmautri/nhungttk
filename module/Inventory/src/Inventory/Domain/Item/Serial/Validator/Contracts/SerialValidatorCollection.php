<?php
namespace Inventory\Domain\Item\Serial\Validator\Contracts;

use Inventory\Domain\Item\Serial\BaseSerial;
use Inventory\Domain\Item\Variant\Validator\Contracts\SerialValidatorInterface;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class SerialValidatorCollection implements SerialValidatorInterface
{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(SerialValidatorInterface $validator)
    {
        if (! $validator instanceof SerialValidatorInterface) {
            throw new InvalidArgumentException("SerialValidatorInterface Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Variant\Validator\Contracts\SerialValidatorInterface::validate()
     */
    public function validate(BaseSerial $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("Header Validator is required! but no is given.");
        }

        foreach ($this->validators as $validator) {
            $validator->validate($rootEntity);
        }
    }
}


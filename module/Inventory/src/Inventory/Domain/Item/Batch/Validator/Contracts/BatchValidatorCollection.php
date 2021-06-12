<?php
namespace Inventory\Domain\Item\Batch\Validator\Contracts;

use Inventory\Domain\Item\Batch\BaseBatch;
use Inventory\Domain\Item\Variant\Validator\Contracts\BatchValidatorInterface;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BatchValidatorCollection implements BatchValidatorInterface
{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(BatchValidatorInterface $validator)
    {
        if (! $validator instanceof BatchValidatorInterface) {
            throw new InvalidArgumentException("SerialValidatorInterface Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Item\Variant\Validator\Contracts\BatchValidatorInterface::validate()
     */
    public function validate(BaseBatch $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("Header Validator is required! but no is given.");
        }

        foreach ($this->validators as $validator) {
            $validator->validate($rootEntity);
        }
    }
}


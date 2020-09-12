<?php
namespace User\Domain\User\Validator\Contracts;

use Inventory\Domain\Warehouse\Validator\Contracts\WarehouseValidatorInterface;
use User\Domain\User\BaseUser;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class UserValidatorCollection implements WarehouseValidatorInterface
{

    private $validators;

    public function add(UserValidatorInterface $validator)
    {
        if (! $validator instanceof UserValidatorInterface) {
            throw new InvalidArgumentException("Header Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Validator\Contracts\WarehouseValidatorInterface::validate()
     */
    public function validate(BaseUser $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("User Validator is required! but none is given.");
        }

        foreach ($this->validators as $validator) {

            // var_dump(\get_class($validator));
            /**
             *
             * @var UserValidatorInterface $validator ;
             */
            $validator->validate($rootEntity);
        }
    }
}


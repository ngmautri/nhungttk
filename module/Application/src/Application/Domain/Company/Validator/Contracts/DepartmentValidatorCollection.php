<?php
namespace Application\Domain\Company\Validator\Contracts;

use Application\Domain\Company\Department\BaseDepartment;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class DepartmentValidatorCollection implements DepartmentValidatorInterface
{

    private $validators;

    public function add(DepartmentValidatorInterface $validator)
    {
        if (! $validator instanceof DepartmentValidatorInterface) {
            throw new InvalidArgumentException("Department Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Validator\Contracts\WarehouseValidatorInterface::validate()
     */
    public function validate(BaseDepartment $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("Department Validator is required! but none is given.");
        }

        foreach ($this->validators as $validator) {

            // var_dump(\get_class($validator));
            /**
             *
             * @var DepartmentValidatorInterface $validator ;
             */
            $validator->validate($rootEntity);
        }
    }
}


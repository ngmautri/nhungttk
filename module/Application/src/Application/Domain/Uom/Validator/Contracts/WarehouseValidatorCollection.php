<?php
namespace Inventory\Domain\Warehouse\Validator\Contracts;

use Inventory\Domain\Warehouse\BaseWarehouse;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseValidatorCollection implements WarehouseValidatorInterface
{

    private $validators;

    public function add(WarehouseValidatorInterface $validator)
    {
        if (! $validator instanceof WarehouseValidatorInterface) {
            throw new InvalidArgumentException("Header Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Validator\Contracts\WarehouseValidatorInterface::validate()
     */
    public function validate(BaseWarehouse $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("WH Validator is required! but none is given.");
        }

        foreach ($this->validators as $validator) {

            // var_dump(\get_class($validator));
            /**
             *
             * @var WarehouseValidatorInterface $validator ;
             */
            $validator->validate($rootEntity);
        }
    }
}


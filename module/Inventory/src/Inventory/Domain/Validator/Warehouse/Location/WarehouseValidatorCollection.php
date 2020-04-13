<?php
namespace Inventory\Domain\Validator\Warehouse;

use Inventory\Domain\Warehouse\AbstractWarehouse;
use Procure\Domain\Exception\InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class WarehouseValidatorCollection implements WarehouseValidatorInterface
{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(WarehouseValidatorInterface $validator)
    {
        if (! $validator instanceof WarehouseValidatorInterface) {
            throw new InvalidArgumentException("Warehouse Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Validator\Warehouse\WarehouseValidatorInterface::validate()
     */
    public function validate(AbstractWarehouse $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("Warehouse Validator is required! but no is given.");
        }

        foreach ($this->validators as $validator) {
            /**
             *
             * @var WarehouseValidatorInterface $validator ;
             */
            $validator->validate($rootEntity);
        }
    }
}


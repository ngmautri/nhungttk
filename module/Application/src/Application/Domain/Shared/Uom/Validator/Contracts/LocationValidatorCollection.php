<?php
namespace Inventory\Domain\Warehouse\Validator\Contracts;

use Inventory\Domain\Warehouse\BaseWarehouse;
use Inventory\Domain\Warehouse\Location\BaseLocation;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class LocationValidatorCollection implements LocationValidatorInterface
{

    private $validators;

    /**
     *
     * @param LocationValidatorInterface $validator
     * @throws InvalidArgumentException
     */
    public function add(LocationValidatorInterface $validator)
    {
        if (! $validator instanceof LocationValidatorInterface) {
            throw new InvalidArgumentException(sprintf("Row Validator is required! %s ", get_class($this)));
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Validator\Contracts\LocationValidatorInterface::validate()
     */
    public function validate(BaseWarehouse $rootEntity, BaseLocation $localEntity)
    {
        if (! $rootEntity instanceof BaseWarehouse) {
            throw new InvalidArgumentException("Document type is not valid.");
        }

        if (! $localEntity instanceof BaseLocation) {
            throw new InvalidArgumentException("Document row type is not valid.");
        }

        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("Location Validator is required! but no is given.");
        }

        foreach ($this->validators as $validator) {

            /**
             *
             * @var LocationValidatorInterface $validator ;
             */
            $validator->validate($rootEntity, $localEntity);
        }
    }
}


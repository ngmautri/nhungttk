<?php
namespace Application\Domain\Warehouse\Validator\Contracts;

use Application\Domain\Company\BaseCompany;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class CompanyValidatorCollection implements CompanyValidatorInterface
{

    private $validators;

    public function add(CompanyValidatorInterface $validator)
    {
        if (! $validator instanceof CompanyValidatorInterface) {
            throw new InvalidArgumentException("Company Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Warehouse\Validator\Contracts\WarehouseValidatorInterface::validate()
     */
    public function validate(BaseCompany $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("Company Validator is required! but none is given.");
        }

        foreach ($this->validators as $validator) {

            // var_dump(\get_class($validator));
            /**
             *
             * @var CompanyValidatorInterface $validator ;
             */
            $validator->validate($rootEntity);
        }
    }
}


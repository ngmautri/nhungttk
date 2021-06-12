<?php
namespace Application\Domain\Company\AccessControl\Validator\Contracts;

use Application\Domain\Company\AccessControl\BaseRole;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class RoleValidatorCollection implements RoleValidatorInterface

{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(RoleValidatorInterface $validator)
    {
        if (! $validator instanceof RoleValidatorInterface) {
            throw new InvalidArgumentException("RoleValidatorInterfaceValidator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\AccessControl\Validator\Contracts\RoleValidatorInterface::validate()
     */
    public function validate(BaseRole $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("BaseRole is required! but no is given.");
        }

        foreach ($this->validators as $validator) {
            $validator->validate($rootEntity);
        }
    }
}


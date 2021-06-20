<?php
namespace Application\Domain\Company\Brand\Validator\Contracts;

use Application\Domain\Company\Brand\BaseBrand;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class BrandValidatorCollection implements BrandValidatorInterface

{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(BrandValidatorInterface $validator)
    {
        if (! $validator instanceof BrandValidatorInterface) {
            throw new InvalidArgumentException("ItemAssociationValidatorInterface Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\Brand\Validator\Contracts\BrandValidatorInterface::validate()
     */
    public function validate(BaseBrand $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("BaseBrand is required! but no is given.");
        }

        foreach ($this->validators as $validator) {
            $validator->validate($rootEntity);
        }
    }
}


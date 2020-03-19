<?php
namespace module\Procure\src\Procure\Domain\PurchaseOrder\Validator;

use Procure\Domain\Exception\PoInvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class HeaderValidatorCollection implements HeaderValidatorInterface
{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    /**
     *
     * @param HeaderValidatorInterface $validator
     * @throws PoInvalidArgumentException
     */
    public function add(HeaderValidatorInterface $validator)
    {
        if (! $validator instanceof HeaderValidatorInterface) {
            throw new PoInvalidArgumentException("Po Header Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \module\Procure\src\Procure\Domain\PurchaseOrder\Validator\HeaderValidatorInterface::validate()
     */
    public function validate($rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new PoInvalidArgumentException("Po Header Validator is required! but no is given.");
        }

        foreach ($this->validators as $validator) {
            /**
             *
             * @var HeaderValidatorInterface $validator ;
             */
            $validator->validate($rootEntity);
        }
    }
}


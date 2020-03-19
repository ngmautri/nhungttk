<?php
namespace module\Procure\src\Procure\Domain\PurchaseOrder\Validator;

use Procure\Domain\Exception\PoInvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class RowValidatorCollection implements RowValidatorInterface
{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(RowValidatorInterface $validator)
    {
        if (! $validator instanceof HeaderValidatorInterface) {
            throw new PoInvalidArgumentException("Po Row Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \module\Procure\src\Procure\Domain\PurchaseOrder\Validator\RowValidatorInterface::validate()
     */
    public function validate($rootEntity, $localEntity)
    {
        if (count($this->validators) == 0) {
            throw new PoInvalidArgumentException("Po Row Validator is required! but no is given.");
        }

        foreach ($this->validators as $validator) {
            /**
             *
             * @var RowValidatorInterface $validator ;
             */
            $validator->validate($rootEntity, $localEntity);
        }
    }
}


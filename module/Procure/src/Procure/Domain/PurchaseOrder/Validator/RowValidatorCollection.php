<?php
namespace Procure\Domain\PurchaseOrder\Validator;

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
        if (! $validator instanceof RowValidatorInterface) {
            throw new PoInvalidArgumentException(sprintf("Po Row Validator is required! %s ", get_class($this)));
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\PurchaseOrder\Validator\RowValidatorInterface::validate()
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


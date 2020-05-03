<?php
namespace Inventory\Domain\Transaction\Validator\Contracts;

use Inventory\Domain\Transaction\AbstractTrx;
use Inventory\Domain\Transaction\BaseRow;
use InvalidArgumentException;

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

    /**
     *
     * @param RowValidatorInterface $validator
     * @throws InvalidArgumentException
     */
    public function add(RowValidatorInterface $validator)
    {
        if (! $validator instanceof RowValidatorInterface) {
            throw new InvalidArgumentException(sprintf("Row Validator is required! %s ", get_class($this)));
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Transaction\Validator\Contracts\RowValidatorInterface::validate()
     */
    public function validate(AbstractTrx $rootEntity, BaseRow $localEntity)
    {
        if (! $rootEntity instanceof AbstractTrx) {
            throw new InvalidArgumentException("Document type is not valid.");
        }

        if (! $localEntity instanceof BaseRow) {
            throw new InvalidArgumentException("Document row type is not valid.");
        }

        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("Row Validator is required! but no is given.");
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


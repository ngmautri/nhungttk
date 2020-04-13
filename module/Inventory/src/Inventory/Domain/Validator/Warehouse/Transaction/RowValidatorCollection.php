<?php
namespace Inventory\Domain\Validator\Warehouse\Transaction;

use Application\Domain\Util\Translator;
use Inventory\Domain\Warehouse\Transaction\AbstractTransaction;
use Procure\Domain\Exception\InvalidArgumentException;

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
            throw new InvalidArgumentException(Translator::translate("Warehouse Transaction Validator is required!"));
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Validator\Warehouse\Transaction\RowValidatorInterface::validate()
     */
    public function validate(AbstractTransaction $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException(Translator::translate("Warehouse Transaction Validator is required! but no is given."));
        }

        foreach ($this->validators as $validator) {
            /**
             *
             * @var RowValidatorInterface $validator ;
             */
            $validator->validate($rootEntity);
        }
    }
}


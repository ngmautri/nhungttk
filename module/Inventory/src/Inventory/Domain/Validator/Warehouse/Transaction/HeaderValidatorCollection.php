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
class HeaderValidatorCollection implements HeaderValidatorInterface
{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(HeaderValidatorInterface $validator)
    {
        if (! $validator instanceof HeaderValidatorInterface) {
            throw new InvalidArgumentException(Translator::translate("Warehouse Transaction Validator is required!"));
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Inventory\Domain\Validator\Warehouse\Transaction\HeaderValidatorInterface::validate()
     */
    public function validate(AbstractTransaction $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException(Translator::translate("Warehouse Transaction Validator is required! but no is given."));
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


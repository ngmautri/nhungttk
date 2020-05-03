<?php
namespace Inventory\Domain\Transaction\Validator\Contracts;

use Inventory\Domain\Transaction\AbstractTrx;
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

    /**
     *
     * @param HeaderValidatorInterface $validator
     * @throws InvalidArgumentException
     */
    public function add(HeaderValidatorInterface $validator)
    {
        if (! $validator instanceof HeaderValidatorInterface) {
            throw new InvalidArgumentException("Header Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Transaction\Validator\Contracts\HeaderValidatorInterface::validate()
     */
    public function validate(AbstractTrx $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("Header Validator is required! but none is given.");
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


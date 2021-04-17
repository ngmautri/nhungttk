<?php
namespace Application\Domain\Company\AccountChart\Validator\Contracts;

use Application\Domain\Company\AccountChart\AbstractAccount;
use Application\Domain\Company\AccountChart\AbstractChart;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class AccountValidatorCollection implements AccountValidatorInterface

{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(AccountValidatorInterface $validator)
    {
        if (! $validator instanceof AccountValidatorInterface) {
            throw new InvalidArgumentException("Acc Header Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\AccountChart\Validator\Contracts\AccountValidatorInterface::validate()
     */
    public function validate(AbstractChart $rootEntity, AbstractAccount $localEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("Header Validator is required! but no is given.");
        }

        foreach ($this->validators as $validator) {
            /**
             *
             * @var AccountValidatorInterface $validator ;
             */
            $validator->validate($rootEntity, $localEntity);
        }
    }
}


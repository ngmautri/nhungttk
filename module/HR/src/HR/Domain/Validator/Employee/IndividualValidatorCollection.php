<?php
namespace HR\Domain\Validator\Employee;

use InvalidArgumentException;
use HR\Domain\Employee\BaseIndividual;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class IndividualValidatorCollection implements IndividualValidatorInterface
{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(IndividualValidatorInterface $validator)
    {
        if (! $validator instanceof IndividualValidatorInterface) {
            throw new InvalidArgumentException("Individual Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Domain\Validator\Employee\IndividualValidatorInterface::validate()
     */
    public function validate(BaseIndividual $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("Individual Validator is required! but none is given.");
        }

        foreach ($this->validators as $validator) {
            /**
             *
             * @var IndividualValidatorInterface $validator ;
             */
            $validator->validate($rootEntity);
        }
    }
}


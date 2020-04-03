<?php
namespace Procure\Domain\Validator;

use Procure\Domain\AbstractDoc;
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
            throw new InvalidArgumentException("Po Header Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\Validator\HeaderValidatorInterface::validate()
     */
    public function validate(AbstractDoc $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("Header Validator is required! but no is given.");
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


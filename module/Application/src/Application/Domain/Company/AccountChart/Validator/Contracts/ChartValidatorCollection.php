<?php
namespace Application\Domain\Company\AccountChart\Validator\Contracts;

use Application\Domain\Company\AccountChart\AbstractChart;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ChartValidatorCollection implements ChartValidatorInterface
{

    /**
     *
     * @var $validators[];
     */
    private $validators;

    public function add(ChartValidatorInterface $validator)
    {
        if (! $validator instanceof ChartValidatorInterface) {
            throw new InvalidArgumentException("Chart Header Validator is required!");
        }

        $this->validators[] = $validator;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\AccountChart\Validator\Contracts\ChartValidatorInterface::validate()
     */
    public function validate(AbstractChart $rootEntity)
    {
        if (count($this->validators) == 0) {
            throw new InvalidArgumentException("Header Validator is required! but no is given.");
        }

        foreach ($this->validators as $validator) {
            /**
             *
             * @var ChartValidatorInterface $validator ;
             */
            $validator->validate($rootEntity);
        }
    }
}


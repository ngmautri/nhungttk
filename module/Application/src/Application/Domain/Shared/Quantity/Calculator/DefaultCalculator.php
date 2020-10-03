<?php
namespace Application\Domain\Shared\Quantity\Caculator;

use Application\Domain\Shared\Quantity\Contracts\Calculator;

final class DefaultCalculator implements Calculator
{

    /**
     *
     * {@inheritdoc}
     */
    public static function supported()
    {
        return true;
    }

    public function multiply($amount, $multiplier)
    {}

    public function compare($a, $b)
    {}

    /**
     *
     * @param int $amount
     * @throws \UnexpectedValueException
     */
    private function assertInteger($amount)
    {
        if (filter_var($amount, FILTER_VALIDATE_INT) === false) {
            throw new \UnexpectedValueException('The result of arithmetic operation is not an integer');
        }
    }

}

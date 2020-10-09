<?php
namespace Application\Domain\Shared\Calculator;

use Application\Domain\Shared\Calculator\Contracts\Calculator;
use Money\Number;
use Webmozart\Assert\Assert;

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

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Quantity\Contracts\Calculator::subtract()
     */
    public function subtract($amount, $subtrahend)
    {
        Assert::numeric($amount);
        Assert::numeric($subtrahend);

        $result = $amount - $subtrahend;

        $this->assertIntegerBounds($result);
        return $result;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Quantity\Contracts\Calculator::divide()
     */
    public function divide($amount, $divisor)
    {
        Assert::numeric($amount);
        Assert::numeric($divisor);

        $result = $amount / $divisor;

        $this->assertIntegerBounds($result);
        return $result;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Quantity\Contracts\Calculator::multiply()
     */
    public function multiply($amount, $multiplier)
    {
        Assert::numeric($amount);
        Assert::numeric($multiplier);

        $result = $amount * $multiplier;
        $this->assertIntegerBounds($result);
        return $result;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Quantity\Contracts\Calculator::add()
     */
    public function add($amount, $addend)
    {
        Assert::numeric($amount);
        Assert::numeric($addend);

        $result = $amount + $addend;

        $this->assertIntegerBounds($result);

        return $result;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\Quantity\Contracts\Calculator::compare()
     */
    public function compare($a, $b)
    {
        return ($a < $b) ? - 1 : (($a > $b) ? 1 : 0);
    }

    /**
     *
     * @param int $amount
     * @throws \OverflowException
     * @throws \UnderflowException
     */
    private function assertIntegerBounds($amount)
    {
        \var_dump($amount);
        if ($amount > PHP_INT_MAX) {
            throw new \OverflowException('the maximum allowed integer (PHP_INT_MAX) overflowed ');
        } elseif ($amount < ~ PHP_INT_MAX) {
            throw new \UnderflowException('the minimum allowed integer (PHP_INT_MAX) underflowed' );
        }
    }

}

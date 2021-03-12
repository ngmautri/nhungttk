<?php
namespace HR\Domain\ValueObject\Salary;

use Application\Domain\Shared\ValueObject;
use Application\Domain\Shared\Calculator\DefaultCalculator;
use Application\Domain\Shared\Calculator\Contracts\Calculator;
use Application\Domain\Shared\Quantity\Quantity;
use Money\Money;
use Webmozart\Assert\Assert;

/**
 *
 * @author nguyen mau tri - ngmautri@gmail.com
 *
 */
final class SalaryMoney extends ValueObject implements \JsonSerializable
{

    private $salaryMoney;

    private static $calculator;

    private static $calculators = [
        DefaultCalculator::class
    ];

    /**
     *
     * @param Money $priceMoney
     * @param Quantity $quantity
     */
    public function __construct(Money $salaryMoney)
    {
        Assert::notNull($salaryMoney, 'Salary should not be null');

        $this->salaryMoney = $salaryMoney;
    }

    public function add(SalaryMoney ...$addends)
    {
        $amount = $this->getSalaryMoney();

        foreach ($addends as $addend) {
            $amount = $amount->add($addend->getSalaryMoney());
        }
        return new self($amount);
    }

    public function subtract(SalaryMoney ...$addends)
    {
        $amount = $this->getSalaryMoney();

        foreach ($addends as $addend) {
            $amount = $amount->subtract($addend->getSalaryMoney());
        }
        return new self($amount);
    }

    public function multiply($multiplier)
    {
        $amount = $this->getSalaryMoney();

        $amount = $amount->multiply($multiplier);
        return new self($amount);
    }

    public function divide($divisor)
    {
        $amount = $this->getSalaryMoney();
        $amount = $amount->divide($divisor);
        return new self($amount);
    }

    public function makeSnapshot()
    {}

    public function getAttributesToCompare()
    {}

    public function jsonSerialize()
    {}

    /**
     *
     * @return \Money\Money
     */
    public function getSalaryMoney()
    {
        return $this->salaryMoney;
    }
}

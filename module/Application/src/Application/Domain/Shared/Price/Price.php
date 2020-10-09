<?php
namespace Application\Domain\Shared\Price;

use Application\Domain\Shared\Calculator\DefaultCalculator;
use Application\Domain\Shared\Calculator\Contracts\Calculator;
use Application\Domain\Shared\Quantity\Quantity;
use Application\Domain\Shared\Uom\UomPair;
use Money\CurrencyPair;
use Money\Money;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use Webmozart\Assert\Assert;

/**
 *
 * @author nguyen mau tri - ngmautri@gmail.com
 *
 */
final class Price implements \jsonserializable
{

    private $priceMoney;

    private $quantity;

    private static $calculator;

    private static $calculators = [
        DefaultCalculator::class
    ];

    private static $converter;

    private static $converters = [
        Converter::class
    ];

    public function __construct(Money $priceMoney, Quantity $quantity)
    {
        if ($quantity == null) {
            throw new \invalidargumentexception('Quantity should be string');
        }

        if ($priceMoney == null) {
            throw new \invalidargumentexception('UoM code should not be empty string');
        }

        $this->priceMoney = $priceMoney;
        $this->quantity = $quantity;
    }

   /**
    *
    * @param UomPair $uomPair
    * @return \Application\Domain\Shared\Price\Price
    */
    public function convertQuantiy(UomPair $uomPair)
    {
        $qty = $this->getQuantity()->convert($uomPair);
        return new self($this->getPriceMoney(), $qty);
    }

    /**
     *
     * @param CurrencyPair $currencyPair
     * @throws \RuntimeException
     * @return \Application\Domain\Shared\Price\Price
     */
    public function convertCurrency(CurrencyPair $currencyPair)
    {
        if ($currencyPair->getBaseCurrency() == $this->getPriceMoney()->getCurrency()) {

            $m = $this->getPriceMoney()->multiply($currencyPair->getConversionRatio());
            return new self(new Money($m->getAmount(), $currencyPair->getCounterCurrency()), $this->getQuantity());
        }

        throw new \RuntimeException('can not conver currency');
    }

    /**
     *
     * @return \Application\Domain\Shared\Price\Price
     */
    public function getUnitPrice()
    {
        if ($this->getQuantity()->isOneUnit()) {
            return $this;
        }

        $qty = new Quantity(1, $this->getQuantity()->getUom());
        $a = $this->getQuantity()->getAmount();
        $m = $this->divideMoney($a);
        return new self($m->getPriceMoney(), $qty);
    }

    /**
     * Add money and quantity
     *
     * @param Price ...$addends
     * @return \Application\Domain\Shared\Price\Price
     */
    public function add(Price ...$addends)
    {
        $calculator = $this->getCalculator();

        foreach ($addends as $addend) {
            $this->assertSameUnit($addend);
            $this->assertSameCurrency($addend);

            $a1 = $this->getPriceMoney()->getAmount();
            $a2 = $addend->getPriceMoney()->getAmount();
            $amount = $calculator->add($a1, $a2);

            $qty = $this->getQuantity()->add($addend->getQuantity());
        }

        return new self(new Money($amount, $this->getPriceMoney()->getCurrency()), $qty);
    }

    /**
     * Multiply money and quantity
     *
     * @param int $multiplier
     * @param string $roundingMode
     * @return \Application\Domain\Shared\Price\Price
     */
    public function multiply($multiplier, $roundingMode = null)
    {
        Assert::greaterThan($multiplier, 0, 'Multipler must greater than zero');
        $a1 = $this->getPriceMoney();
        $m = $a1->multiply($multiplier);

        $qty = $this->getQuantity()->multiply($multiplier);

        return new self($m, $qty);
    }

    /**
     * Add money, Same quantity
     *
     * @param Money ...$addends
     * @return \Application\Domain\Shared\Price\Price
     */
    public function addMoney(Money ...$addends)
    {
        foreach ($addends as $addend) {
            $a1 = $this->getPriceMoney();
            $m = $a1->add($addend);
        }

        return new self($m, $this->getQuantity());
    }

    /**
     * Subtruct money, Same quantity
     *
     * @param Price ...$subtrahends
     * @return \Application\Domain\Shared\Price\Price
     */
    public function subtractMoney(Money ...$subtrahends)
    {
        foreach ($subtrahends as $subtrahend) {
            $a1 = $this->getPriceMoney();
            $m = $a1->subtract($subtrahend);

            Assert::greaterThan($m->getAmount(), 0, 'Price can not be negative!');
        }

        return new self($m, $this->getQuantity());
    }

    /**
     * Multily money, Same quantity
     *
     * @param int $multiplier
     * @param string $roundingMode
     * @return \Application\Domain\Shared\Price\Price
     */
    public function multiplyMoney($multiplier, $roundingMode = null)
    {
        Assert::greaterThan($multiplier, 0, 'Multipler must greater than zero');
        $a1 = $this->getPriceMoney();
        $m = $a1->multiply($multiplier);
        return new self($m, $this->getQuantity());
    }

    /**
     * Divide money, Same quantity
     *
     * @param int $divisor
     * @param string $roundingMode
     * @return \Application\Domain\Shared\Price\Price
     */
    public function divideMoney($divisor, $roundingMode = null)
    {
        Assert::greaterThan($divisor, 0, 'Division by zero');
        $a1 = $this->getPriceMoney();
        $m = $a1->divide($divisor);

        $qty = $this->getQuantity();

        return new self($m, $qty);
    }

    /**
     *
     * @param Price $other
     * @return boolean
     */
    public function equals(Price $other)
    {
        return $this->isSameQuantity($other) && $this->getPriceMoney()->equals($other->getPriceMoney());
    }

    /**
     *
     * @param Price $other
     * @return int
     */
    public function compare(Price $other)
    {
        $this->assertSameQuantity($other);
        $this->assertSameCurrency($other);

        $a1 = $this->getPriceMoney()->getAmount();
        $a2 = $other->getPriceMoney()->getAmount();

        return $this->getCalculator()->compare($a1, $a2);
    }

    /**
     *
     * @param Price $other
     * @return boolean
     */
    public function isSameUnit(Price $other)
    {
        return $this->getQuantity()
            ->getUom()
            ->equals($other->getQuantity()
            ->getUom());
    }

    /**
     *
     * @param Price $other
     * @return boolean
     */
    public function isSameQuantity(Price $other)
    {
        return $this->getQuantity()->equals($other->getQuantity());
    }

    public function isSameCurrency(Price $other)
    {
        return $this->getPriceMoney()->isSameCurrency($other->getPriceMoney());
    }

    public function __toString()
    {
        $currencies = new ISOCurrencies();
        $moneyFormatter = new DecimalMoneyFormatter($currencies);
        $moneyFormatter->format($this->getPriceMoney()); // outputs 1.00

        return \sprintf('%s %s per %s', $moneyFormatter->format($this->getPriceMoney()), $this->getPriceMoney()->getCurrency(), $this->getQuantity()->__toString());
    }

    public function jsonserialize()
    {}

    /**
     *
     * @param Price $other
     * @throws \InvalidArgumentException
     */
    private function assertSameUnit(Price $other)
    {
        if (! $this->isSameUnit($other)) {
            throw new \InvalidArgumentException('Measurement unit must be identical');
        }
    }

    private function assertSameQuantity(Price $other)
    {
        if (! $this->isSameQuantity($other)) {
            throw new \InvalidArgumentException('Quantity must be identical');
        }
    }

    /**
     *
     * @param Price $other
     * @throws \InvalidArgumentException
     */
    private function assertSameCurrency(Price $other)
    {
        if (! $this->isSameCurrency($other)) {
            throw new \InvalidArgumentException('Currency must be identical');
        }
    }

    private function assertSameMoneyCurrency(Money $other)
    {
        if (! $this->getPriceMoney()
            ->getCurrency()
            ->equals($other->getCurrency())) {
            throw new \InvalidArgumentException('Currency must be identical');
        }
    }

    /**
     *
     * @throws \RuntimeException
     * @return object
     */
    private static function initializeCalculator()
    {
        $calculators = self::$calculators;

        foreach ($calculators as $calculator) {
            /** @var Calculator $calculator */
            if ($calculator::supported()) {
                return new $calculator();
            }
        }

        throw new \RuntimeException('Cannot find calculator for money calculations');
    }

    /**
     *
     * @return Calculator
     */
    private function getCalculator()
    {
        if (null === self::$calculator) {
            self::$calculator = self::initializeCalculator();
        }

        return self::$calculator;
    }

    private static function initializeConverter()
    {
        $converters = self::$converters;

        foreach ($converters as $converter) {
            return new $converter();
        }

        throw new \RuntimeException('Cannot find any quantity converter!');
    }

    /**
     *
     * @return Converter
     */
    private function getConverter()
    {
        if (null === self::$converter) {
            self::$converter = self::initializeConverter();
        }

        return self::$converter;
    }

    /**
     *
     * @return Money
     */
    public function getPriceMoney()
    {
        return $this->priceMoney;
    }

    /**
     *
     * @return \Application\Domain\Shared\Quantity\Quantity
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
}

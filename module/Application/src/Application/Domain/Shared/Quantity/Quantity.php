<?php
namespace Application\Domain\Shared\Quantity;

use Application\Domain\Shared\Calculator\DefaultCalculator;
use Application\Domain\Shared\Calculator\Contracts\Calculator;
use Application\Domain\Shared\Uom\Uom;
use Application\Domain\Shared\Uom\UomPair;
use Webmozart\Assert\Assert;

/**
 *
 * @author nguyen mau tri - ngmautri@gmail.com
 *
 */
final class Quantity implements \jsonserializable
{

    private $amount;

    private $uom;

    private static $calculator;

    private static $calculators = [
        DefaultCalculator::class
    ];

    private static $converter;

    private static $converters = [
        Converter::class
    ];

    /**
     *
     * @param UomPair $uomPair
     * @return void|\Application\Domain\Shared\Quantity\Quantity
     */
    public function convert(UomPair $uomPair)
    {
        return $this->getConverter()->convertToBaseUom($this, $uomPair);
    }

    public function isOneUnit()
    {
        return $this->getAmount() == 1;
    }

    public function getUnitQuantity()
    {
        if ($this->isOneUnit()) {
            return $this;
        }

        return new Quantity(1, $this->getUom());
    }

    public function __construct($amount, Uom $uom)
    {
        Assert::notNull($uom, 'Uom should not be null');
        Assert::numeric($amount, 'Amount should not be numeric');
        Assert::greaterThan($amount, 0, 'Amount should be greater then zero');

        $this->amount = $amount;
        $this->uom = $uom;
    }

    /**
     *
     * @param Quantity ...$addends
     * @return \Application\Domain\Shared\Quantity\Quantity
     */
    public function add(Quantity ...$addends)
    {
        $amount = $this->amount;
        $calculator = $this->getCalculator();

        foreach ($addends as $addend) {
            $this->assertSameUnit($addend);
            $amount = $calculator->add($amount, $addend->amount);
        }

        return new self($amount, $this->getUom());
    }

    /**
     *
     * @param Quantity ...$subtrahends
     * @return \Application\Domain\Shared\Quantity\Quantity
     */
    public function subtract(Quantity ...$subtrahends)
    {
        $amount = $this->amount;
        $calculator = $this->getCalculator();

        foreach ($subtrahends as $subtrahend) {
            $this->assertSameUnit($subtrahend);

            $amount = $calculator->subtract($amount, $subtrahend->amount);
        }

        return new self($amount, $this->getUom());
    }

    /**
     *
     * @param int $multiplier
     * @param string $roundingMode
     * @return \Application\Domain\Shared\Quantity\Quantity
     */
    public function multiply($multiplier, $roundingMode = null)
    {
        $amount = $this->getCalculator()->multiply($this->amount, $multiplier);
        return new self($amount, $this->getUom());
    }

    /**
     *
     * @param int $divisor
     * @param string $roundingMode
     * @throws \InvalidArgumentException
     * @return \Application\Domain\Shared\Quantity\Quantity
     */
    public function divide($divisor, $roundingMode = null)
    {
        if ($this->getCalculator()->compare($divisor, 0) === 0) {
            throw new \InvalidArgumentException('Division by zero');
        }

        $amount = $this->getCalculator()->divide($this->amount, $divisor);

        return new self($amount, $this->getUom());
    }

    /**
     *
     * @param Quantity $other
     * @return boolean
     */
    public function equals(Quantity $other)
    {
        return $this->isSameUnit($other) && $this->amount === $other->amount;
    }

    /**
     *
     * @param Quantity $other
     * @return int
     */
    public function compare(Quantity $other)
    {
        $this->assertSameUnit($other);
        return $this->getCalculator()->compare($this->amount, $other->amount);
    }

    /**
     *
     * @param Quantity $other
     * @return boolean
     */
    public function isSameUnit(Quantity $other)
    {
        return $this->getUom()->equals($other->getUom());
    }

    public function __toString()
    {
        return \sprintf('%s %s', $this->getAmount(), $this->getUom()->getUomName());
    }

    public function jsonserialize()
    {}

    /**
     *
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     *
     * @return \Application\Domain\Shared\Uom\Uom
     */
    public function getUom()
    {
        return $this->uom;
    }

    private function assertSameUnit(Quantity $other)
    {
        if (! $this->isSameUnit($other)) {
            throw new \InvalidArgumentException('Measurement unit must be identical');
        }
    }

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
}

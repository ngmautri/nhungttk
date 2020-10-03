<?php
namespace Application\Domain\Shared\Quantity;

use Application\Domain\Shared\Uom\Uom;

/**
 *
 * @author nguyen mau tri - ngmautri@gmail.com
 *
 */
class Quantity implements \jsonserializable
{

    private $amount;

    private $uom;

    private static $calculator;

    private static $calculators;

    /**
     *
     * @param string $uomname
     * @throws \invalidargumentexception
     */
    public function __construct($amount, Uom $uom)
    {
        if ($uom == null) {
            throw new \invalidargumentexception('uom should be string');
        }

        if ($amount == null) {
            throw new \invalidargumentexception('uom code should not be empty string');
        }

        $this->amount = $amount;
        $this->uom = $uom;
    }

    public function compare(Quantity $other)
    {
        $this->assertSameCurrency($other);

        return $this->getCalculator()->compare($this->amount, $other->amount);
    }

    private function assertSameCurrency(Quantity $other)
    {
        if (! $this->isSameCurrency($other)) {
            throw new \InvalidArgumentException('Currencies must be identical');
        }
    }

    public function jsonserialize()
    {}
    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @return \Application\Domain\Shared\Uom\Uom
     */
    public function getUom()
    {
        return $this->uom;
    }

}

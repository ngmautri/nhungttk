<?php
namespace ApplicationTest\UtilityTest;

use Money\Money;
use PHPUnit_Framework_TestCase;

class MoneyTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        $fiveEur = Money::VND(150500);

        $tenEur = $fiveEur->add($fiveEur);

        list($part1, $part2, $part3) = $tenEur->allocate([1, 1, 1]);
        assert($part1->equals(Money::EUR(334)));
        assert($part2->equals(Money::EUR(333)));
        assert($part3->equals(Money::EUR(333)));
    }
}
;
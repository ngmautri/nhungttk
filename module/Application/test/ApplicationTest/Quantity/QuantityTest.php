<?php
namespace ApplicationTest\Quantity;

use Application\Domain\Shared\Money\MoneyParser;
use Application\Domain\Shared\Quantity\Quantity;
use Application\Domain\Shared\Uom\Uom;
use Money\Formatter\IntlMoneyFormatter;
use PHPUnit_Framework_TestCase;
use Application\Domain\Shared\Money\MoneyFormatter;

class QuantityTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        $qty = new Quantity(15, Uom::KILOGRAM());
        $qty1 = new Quantity(16, Uom::EACH());
        $result = $qty->multiply('12');
        // echo $result;

        $money = MoneyParser::parseFromDecimal('334,15', 'eur');
        echo $money->getAmount() / 100; // outputs 100000
        echo $money->getCurrency(); // outputs 100000

        echo "\n";
        echo MoneyFormatter::format($money);
    }
}
<?php
namespace ApplicationTest\Price;

use Application\Domain\Shared\Price\Price;
use Application\Domain\Shared\Quantity\Quantity;
use Application\Domain\Shared\Uom\Uom;
use Application\Domain\Shared\Uom\UomPair;
use Money\Money;
use PHPUnit_Framework_TestCase;
use Money\CurrencyPair;
use Money\Currency;
use Application\Domain\Shared\Money\MoneyParser;

class PriceTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        echo PHP_INT_MAX;
        echo "\n===\n";

        // echo (7500000*9900);
        $p = MoneyParser::parseFromDecimal('1500', 'usd');
        $q = new Quantity(2, Uom::BOX());
        $price = new Price($p, $q);
        echo $price . "\n";
        echo "\n===UP \n";
        echo $price->getUnitPrice();
        echo "\n===\n";

        $uomPair = new UomPair(Uom::EACH(), Uom::box(), 25);

        echo $uomPair->getPairName();
        $result = $price->convertQuantiy($uomPair);

        echo "\n===\n";

        echo $result;

        echo "\n===\n";
        echo $result->getUnitPrice();

        echo "\n===\n";

        $currencyPair = new CurrencyPair(new Currency('USD'), new Currency('LAK'), 9900);
        $result1 = $result->convertCurrency($currencyPair);
        echo "\n===\n";

        echo $result1;
        echo "\n===\n";
        echo $result1->getUnitPrice();
    }
}
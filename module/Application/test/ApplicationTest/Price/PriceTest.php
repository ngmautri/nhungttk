<?php
namespace ApplicationTest\Price;

use Application\Domain\Shared\Money\MoneyParser;
use Application\Domain\Shared\Price\Price;
use Application\Domain\Shared\Quantity\Quantity;
use Application\Domain\Shared\Uom\Uom;
use Application\Domain\Shared\Uom\UomPair;
use Money\Currency;
use Money\CurrencyPair;
use PHPUnit_Framework_TestCase;

class PriceTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testCanCreatePrice()
    {
        echo"\n==" . __METHOD__ . "===\n";
        // echo (7500000*9900);
        $p = MoneyParser::parseFromLocalizedDecimal('22.000', 'lak', 'vi_VN');
        $q = new Quantity(2, Uom::BOX());

        $price = new Price($p, $q);
        $this->assertInstanceOf(Price::class, $price);

        \var_dump($price->getMoneyAmountInEn());


        echo"\n==" . __METHOD__ . " end ===\n";
    }

    public function testCanConvertQuantity()
    {
        // echo (7500000*9900);
        $p = MoneyParser::parseFromLocalizedDecimal('22.000', 'lak', 'vi_VN');
        $q = new Quantity(2, Uom::BOX());
        $price = new Price($p, $q);
        $uomPair = new UomPair(Uom::EACH(), Uom::box(), 25);
        $result = $price->convertQuantiy($uomPair);
        $this->assertInstanceOf(Price::class, $result);

        echo "\n Converting Quantity ===\n";
        echo $uomPair->getPairName();
    }

    public function testCanConvertCurrency()
    {
        echo"\n==" . __METHOD__ . "===\n";
        $p = MoneyParser::parseFromLocalizedDecimal('9200000', 'lak', 'vi_VN');
        $q = new Quantity(2, Uom::BOX());
        $price = new Price($p, $q);

        $currencyPair = new CurrencyPair(new Currency('USD'), new Currency('LAK'), 9200);
        $result1 = $price->convertCurrency($currencyPair);
        $this->assertInstanceOf(Price::class, $result1);
        echo $result1;

        echo"\n==" . __METHOD__ . " end ===\n";
    }

    public function testCanNotConvertCurrency()
    {
        echo"\n==" . __METHOD__ . "===\n";

        $p = MoneyParser::parseFromLocalizedDecimal('22.000', 'lak', 'vi_VN');
        $q = new Quantity(2, Uom::BOX());
        $price = new Price($p, $q);

        $currencyPair = new CurrencyPair(new Currency('LAK'), new Currency('USD'), 9200);

        $this->expectException(\InvalidArgumentException::class);
        $price->convertCurrency($currencyPair);

        echo"\n==" . __METHOD__ . " end ===\n";

    }
}
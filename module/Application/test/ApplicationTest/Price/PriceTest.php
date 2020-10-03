<?php
namespace ApplicationTest\Price;
use Application\Domain\Shared\Price\Price;
use Application\Domain\Shared\Quantity\Quantity;
use Application\Domain\Shared\Uom\Uom;
use Application\Domain\Shared\Uom\UomPair;
use Money\Money;
use PHPUnit_Framework_TestCase;


class PriceTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        $p = Money::LAK(7500000);
        $q = new Quantity(5, Uom::box());
        $price = new Price($p, $q);
        echo $price . "\n";


        echo "\n=== UP\n";

        echo $price->getUnitPrice();
        echo "\n===\n";

        $p = Money::LAK(15000000);
        $q = new Quantity(1, Uom::BOX());

        $price1 = new Price($p, $q);
        echo $price1;
        // \var_dump($price->compare($price1));

        \var_dump($price->equals($price1));

       // echo ($price->multiply(12));
        echo "\n===\n";

        $uomPair = new UomPair(Uom::EACH(), Uom::box(),26);
        $result = $price->convert($uomPair);
        echo "\n===\n";
        echo $result;

        echo "\n===\n";

        echo $result->getUnitPrice();
    }
}
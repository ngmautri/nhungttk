<?php
namespace ApplicationTest\Quantity;
use Application\Domain\Shared\Quantity\Quantity;
use PHPUnit_Framework_TestCase;
use Application\Domain\Shared\Uom\Uom;
use Application\Domain\Shared\Uom\UomPair;

class QuantityConvertTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        $qty = new Quantity(650, Uom::LITER());
        $uomPair = new UomPair(Uom::LITER(), Uom::TANK(), 200);
        $result =  $qty->convert($uomPair);
        echo $qty;
        echo "\n";
        echo $result;
    }
}
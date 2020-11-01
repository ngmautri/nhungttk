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
        $qty = new Quantity(1, Uom::TANK());
        $uomPair = new UomPair(Uom::LITER(), $qty->getUom(), 17);
        $result = $qty->convert($uomPair);
        echo $qty;
        echo "\n";
        echo $result;
    }
}
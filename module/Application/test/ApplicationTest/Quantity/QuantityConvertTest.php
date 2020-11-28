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

    public function testCanConvert()
    {
        $qty = new Quantity(1, Uom::TANK());
        $uomPair = new UomPair(Uom::LITER(), $qty->getUom(), 17);
        $expectedQty = new Quantity(17, Uom::LITER());

        $this->assertTrue($expectedQty->equals($qty->convert($uomPair)));
    }

    public function testCanNotConvert()
    {
        $qty = new Quantity(1, Uom::TANK());
        $uomPair = new UomPair(Uom::LITER(), Uom::LITER(), 17);
        $this->expectException(\InvalidArgumentException::class);
        $qty->convert($uomPair);
    }
}
<?php
namespace ApplicationTest\Quantity;

use Application\Domain\Shared\Quantity\Quantity;
use Application\Domain\Shared\Uom\Uom;
use PHPUnit_Framework_TestCase;

class QuantityTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testCanCreate()
    {
        $qty = new Quantity(15, Uom::KILOGRAM());
        $this->assertInstanceOf(Quantity::class, $qty);
    }

    public function testCanNotCreate()
    {
        $this->expectException(\InvalidArgumentException::class);
        $qty = new Quantity('SDC', Uom::KILOGRAM());
    }

    public function testEqual()
    {
        $qty1 = new Quantity(15, Uom::KILOGRAM());
        $qty2 = new Quantity(15, Uom::KILOGRAM());
        $this->assertTrue($qty1->equals($qty2));
    }

    public function testNotEqual()
    {
        $qty1 = new Quantity(15, Uom::KILOGRAM());
        $qty2 = new Quantity(15, Uom::BAG());
        $this->assertFalse($qty1->equals($qty2));
    }

    public function testAdd()
    {
        $qty1 = new Quantity(15, Uom::KILOGRAM());
        $qty2 = new Quantity(15, Uom::KILOGRAM());
        $qty1->add($qty2);
        $this->assertEquals(new Quantity(30, Uom::KILOGRAM()), $qty1->add($qty2));
    }

    public function testAddDifferentUom()
    {
        $qty1 = new Quantity(15, Uom::KILOGRAM());
        $qty2 = new Quantity(15, Uom::G());

        $this->expectException(\InvalidArgumentException::class);
        $qty1->add($qty2);
    }
}
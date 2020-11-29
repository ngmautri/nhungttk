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

    public function testSubtract()
    {
        $qty1 = new Quantity(15, Uom::KILOGRAM());
        $qty2 = new Quantity(14, Uom::KILOGRAM());
        $this->assertEquals(new Quantity(1, Uom::KILOGRAM()), $qty1->subtract($qty2));
    }

    public function testDivide()
    {
        $qty1 = new Quantity(15, Uom::KILOGRAM());
        $this->assertEquals(new Quantity(5, Uom::KILOGRAM()), $qty1->divide(3));
    }

    public function testDivideZero()
    {
        $qty1 = new Quantity(15, Uom::KILOGRAM());
        $this->expectException(\InvalidArgumentException::class);
        $this->assertEquals(new Quantity(5, Uom::KILOGRAM()), $qty1->divide(0));
    }

    public function testDivideNegative()
    {
        $qty1 = new Quantity(15, Uom::KILOGRAM());
        $this->expectException(\InvalidArgumentException::class);
        $qty1->divide(- 1);
    }

    public function testMultiple()
    {
        $qty1 = new Quantity(15, Uom::KILOGRAM());
        $this->assertEquals(new Quantity(45, Uom::KILOGRAM()), $qty1->multiply(3));
    }

    public function testMultipleNegative()
    {
        $qty1 = new Quantity(15, Uom::KILOGRAM());
        $this->expectException(\InvalidArgumentException::class);
        $qty1->multiply(0);
    }
}
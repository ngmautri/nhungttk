<?php
namespace ApplicationTest\Quantity;

use Application\Domain\Shared\Quantity\Quantity;
use Application\Domain\Shared\Uom\Uom;
use PHPUnit_Framework_TestCase;

class QuantityTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}


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
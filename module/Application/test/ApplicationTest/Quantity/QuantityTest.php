<?php
namespace ApplicationTest\Quantity;
use Application\Domain\Shared\Quantity\Quantity;
use PHPUnit_Framework_TestCase;
use Application\Domain\Shared\Uom\Uom;

class QuantityTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        $qty = new Quantity(15, Uom::KILOGRAM());
        $qty1 = new Quantity(16, Uom::EACH());
        $result = $qty->multiply('12');
        echo $result;
    }
}
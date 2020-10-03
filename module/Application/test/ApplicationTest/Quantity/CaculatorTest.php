<?php
namespace ApplicationTest\Quantity;
use Application\Domain\Shared\Quantity\Calculator\DefaultCalculator;
use PHPUnit_Framework_TestCase;

class CaculatorTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        $cal = new DefaultCalculator();
        $result = $cal->add(14, 12);
        \var_dump($result);
    }
}
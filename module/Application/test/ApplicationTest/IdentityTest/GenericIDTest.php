<?php
namespace ApplicationTest\Identity;

use Application\Domain\Shared\Identity\GenericId;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class GenericIDTest extends PHPUnit_Framework_TestCase
{

    public function testCanCreate()
    {
        $this->assertInstanceOf(GenericId::class, new GenericID(1));
    }

    public function testCannotCreate()
    {
        $this->expectException(InvalidArgumentException::class);

        new GenericID('-sdfdsf12');
    }

    public function testCanBeUsedAsString()
    {
        $id = new GenericID(1);
        $this->assertEquals(1, $id->getValue());
    }

    public function testEqual()
    {
        $id1 = new GenericID(1);
        $id2 = new GenericID(1);
        $this->assertTrue($id1->equals($id2));
    }

    public function testNotEqual()
    {
        $id1 = new GenericID(1);
        $id2 = new GenericID(10);
        $this->assertFalse($id1->equals($id2));
    }
}
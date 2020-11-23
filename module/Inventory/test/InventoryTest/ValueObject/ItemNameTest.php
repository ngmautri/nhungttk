<?php
namespace InventoryTest\ValueObject;

use Inventory\Domain\Item\ValueObject\ItemName;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class ItemNameTest extends PHPUnit_Framework_TestCase
{

    public function testCanCreate()
    {
        $this->assertInstanceOf(ItemName::class, new ItemName('safadsfd'));
    }

    public function testCannotCreate()
    {
        $itemName = '1$20000000000000000000000000000000000000000000000000000000';
        echo \strlen($itemName);
        $itemName = '    s';
        $this->expectException(InvalidArgumentException::class);
        $itemName = new ItemName($itemName);
        echo $itemName->getValue();
    }

    public function testCanBeUsedAsString()
    {
        $itemName = new ItemName('123');
        $this->assertEquals('123', $itemName->getValue());
    }

    public function testEqual()
    {
        $vo1 = new ItemName('123');
        $vo2 = new ItemName('123');
        $this->assertTrue($vo1->equals($vo2));
    }

    public function testNotEqual()
    {
        $vo1 = new ItemName('123');
        $vo2 = new ItemName('1234');
        $this->assertFalse($vo1->equals($vo2));
    }
}
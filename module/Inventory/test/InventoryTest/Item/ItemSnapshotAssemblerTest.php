<?php
namespace InventoryTest\Item;

use Inventory\Domain\Item\ItemSnapshotAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class ItemSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = ItemSnapshotAssembler::findMissingPropsInBaseItem();
            // $result = GenericSnapshotAssembler::findMissingPropsInTargetObject(NmtInventoryItem::class, BaseItem::class);

            // BaseItem::createSnapshotBaseProps();
            // var_dump($result);
            ItemSnapshotAssembler::createIndexDoc();
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
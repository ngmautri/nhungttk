<?php
namespace InventoryTest\Item\CodeGenerate;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Application\Entity\NmtInventoryItem;
use Inventory\Domain\Item\AbstractItem;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\GenericItemSnapshot;

class SnapshotCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = GenericSnapshotAssembler::findMissingProps(NmtInventoryItem::class, GenericItem::class);
            // $result = GenericSnapshotAssembler::findMissingProps(GenericItem::class, GenericItemSnapshot::class);

            $result = GenericSnapshotAssembler::createAllSnapshotProps(AbstractItem::class);

            // $result = GenericSnapshotAssembler::createAllSnapshotProps(UomPair::class);
            // $result = GenericSnapshotAssembler::createSnapshotProps(UomGroup::class, BaseUomGroup::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
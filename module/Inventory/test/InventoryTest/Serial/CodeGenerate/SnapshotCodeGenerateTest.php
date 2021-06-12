<?php
namespace InventoryTest\Item\CodeGenerate;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Inventory\Domain\Item\Serial\AbstractSerial;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class SnapshotCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = GenericSnapshotAssembler::findMissingProps(NmtInventoryItem::class, GenericItem::class);
            // $result = GenericSnapshotAssembler::findMissingProps(GenericItem::class, GenericItemSnapshot::class);

            // $result = GenericSnapshotAssembler::cre(NmtInventoryItem::class);

            $result = GenericSnapshotAssembler::createAllSnapshotProps(AbstractSerial::class);
            // $result = GenericSnapshotAssembler::printAllSnapshotPropsInArrayFormat(AbstractSerial::class);

            // $result = GenericSnapshotAssembler::findMissingProps(NmtInventoryItemSerial::class, AbstractSerial::class);
            // $result = GenericSnapshotAssembler::findMissingProps(NmtInventoryItemVariantAttribute::class, AbstractVariantAttribute::class);
            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
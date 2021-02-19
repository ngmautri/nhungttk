<?php
namespace ProcureTest\QR\CodeGenerate;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Inventory\Domain\Item\GenericItem;
use Inventory\Domain\Item\ItemSnapshot;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class SnapshotCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            $result = GenericSnapshotAssembler::findMissingProps(GenericItem::class, ItemSnapshot::class);
            // $result = GenericSnapshotAssembler::findMissingProps(AppUomGroupMember::class, UomPair::class);

            // $result = GenericSnapshotAssembler::createAllSnapshotProps(UomGroup::class);
            // $result = GenericSnapshotAssembler::createAllSnapshotProps(UomPair::class);
            // $result = GenericSnapshotAssembler::createSnapshotProps(UomGroup::class, BaseUomGroup::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
<?php
namespace ApplicationTest\Department\CodeGenerate;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Inventory\Domain\Warehouse\Location\LocationSnapshot;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class SnapshotCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = GenericSnapshotAssembler::findMissingProps(NmtInventoryWarehouse::class, AbstractWarehouse::class);
            // $result = GenericSnapshotAssembler::findMissingProps(NmtInventoryWarehouseLocation::class, AbstractLocation::class);

            // $result = GenericSnapshotAssembler::findMissingProps(AbstractAccount::class, AccountSnapshot::class);

            // $result = GenericSnapshotAssembler::printAllSnapshotPropsInArrayFormat(ChartSnapshot::class);
            // $result = GenericSnapshotAssembler::createAllSnapshotProps(AbstractLocation::class);

            $result = GenericSnapshotAssembler::printAllSnapshotPropsInArrayFormat(LocationSnapshot::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
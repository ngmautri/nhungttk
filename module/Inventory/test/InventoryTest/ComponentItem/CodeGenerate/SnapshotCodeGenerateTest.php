<?php
namespace InventoryTest\ComponentItem\CodeGenerate;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Inventory\Domain\Warehouse\Location\LocationSnapshot;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Inventory\Domain\Item\Component\AbstractComponent;
use Application\Entity\NmtInventoryItemComponent;

class SnapshotCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            $result = GenericSnapshotAssembler::findMissingProps(NmtInventoryItemComponent::class, AbstractComponent::class);
            // $result = GenericSnapshotAssembler::findMissingProps(NmtInventoryWarehouseLocation::class, AbstractLocation::class);

            // $result = GenericSnapshotAssembler::findMissingProps(AbstractAccount::class, AccountSnapshot::class);

            // $result = GenericSnapshotAssembler::printAllSnapshotPropsInArrayFormat(ChartSnapshot::class);
            // $result = GenericSnapshotAssembler::createAllSnapshotProps(AbstractLocation::class);

            //$result = GenericSnapshotAssembler::printAllSnapshotPropsInArrayFormat(AbstractComponent::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
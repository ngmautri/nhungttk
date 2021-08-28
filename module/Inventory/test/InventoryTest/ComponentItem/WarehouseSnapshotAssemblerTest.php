<?php
namespace InventoryTest\ComponentItem;

use Doctrine\ORM\EntityManager;
use Inventory\Domain\Warehouse\BaseWarehouse;
use PHPUnit_Framework_TestCase;

class WarehouseSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
    protected $em;

    public function setUp()
    {}

    public function testOther()
    {
        // WarehouseSnapshotAssembler::findMissingPropsInEntity();
        BaseWarehouse::createSnapshotProps();
    }
}
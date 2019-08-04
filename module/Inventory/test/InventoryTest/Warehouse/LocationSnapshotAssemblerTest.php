<?php
namespace InventoryTest\Warehouse;

use PHPUnit_Framework_TestCase;
use Doctrine\ORM\EntityManager;
use Inventory\Domain\Warehouse\Transaction\GI\GIforRepairMachine;
use Inventory\Domain\Warehouse\WarehouseSnapshotAssembler;
use Inventory\Domain\Warehouse\Location\LocationSnapshotAssembler;

class WarehouseSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
    protected $em;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        LocationSnapshotAssembler::createFromSnapshotCode();
    }
}
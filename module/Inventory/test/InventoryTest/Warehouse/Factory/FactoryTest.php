<?php
namespace InventoryTest\Warehouse\Factory;

use Doctrine\ORM\EntityManager;
use Inventory\Domain\Warehouse\WarehouseSnapshot;
use Inventory\Domain\Warehouse\Factory\WarehouseFactory;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class FactoryTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $snapshot = new WarehouseSnapshot();
            $sharedService = null;
            $options = null;

            WarehouseFactory::createFrom($snapshot, $options, $sharedService);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
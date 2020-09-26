<?php
namespace InventoryTest\Specification;

use ApplicationTest\Bootstrap;
use Inventory\Application\Specification\Inventory\InventorySpecificationFactoryImpl;
use PHPUnit_Framework_TestCase;

class OnhandAtWarehouseTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

        $factory = new InventorySpecificationFactoryImpl($em);
        $spec = $factory->getOnhandQuantitySpecification();

        $subject = [
            "itemId" => 3617,
            "docQuantity" => 12,
            "movementDate" => '2020-09-24',
            "warehouseId" => '5'
        ];

        var_dump($spec->isSatisfiedBy($subject));
    }
}
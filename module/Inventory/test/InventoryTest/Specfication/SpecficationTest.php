<?php
namespace InventoryTest\Specification;

use ApplicationTest\Bootstrap;
use Inventory\Application\Specification\Inventory\InventorySpecificationFactoryImpl;
use PHPUnit_Framework_TestCase;

class SpecficationTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

        $factory = new InventorySpecificationFactoryImpl($em);
        $spec = $factory->getOnhandQuantitySpecification();

        $subject = [
            "itemId" => 2427,
            "warehouseId" => 5,
            "movementDate" => "2020-04-02",
            "docQuantity" => 1500
        ];

        var_dump($spec->isSatisfiedBy($subject));
        echo 501 % 500;
    }
}
<?php
namespace InventoryTest\Specification;

use ApplicationTest\Bootstrap;
use Inventory\Application\Specification\Inventory\InventorySpecificationFactoryImpl;
use PHPUnit_Framework_TestCase;

class OnhandTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

        $factory = new InventorySpecificationFactoryImpl($em);
        $spec = $factory->getOnhandQuantityOfMovementSpecification();

        $subject = [
            "itemId" => 2427,
            "movementId" => 1527,
            "docQuantity" => 13
        ];

        var_dump($spec->isSatisfiedBy($subject));
        echo 501 % 500;
    }
}
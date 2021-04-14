<?php
namespace ApplicationTest\Model;

use Application\Application\Specification\InMemory\InMemorySpecificationFactory;
use PHPUnit_Framework_TestCase;

class WarehouseACLSpecTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        $factory = new InMemorySpecificationFactory();
        $spec = $factory->getWarehouseACLSpecification();
        $subject = array(
            "warehouseId" => 11,
            "companyId" => 1,
            "userId" => 45
        );
        var_dump($spec->isSatisfiedBy($subject));
    }
}
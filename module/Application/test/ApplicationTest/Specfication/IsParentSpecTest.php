<?php
namespace ApplicationTest\Model;

use ApplicationTest\Bootstrap;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;

class IsParentSpecTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

        $factory = new \Application\Application\Specification\Zend\ZendSpecificationFactory($em);
        $spec = $factory->getIsParentSpecification();
        $subject = array(
            "userId1" => 47,
            "userId2" => 39
        );
        var_dump($spec->isSatisfiedBy($subject));
    }
}
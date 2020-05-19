<?php
namespace ApplicationTest\Model;

use ApplicationTest\Bootstrap;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;

class SpecficationTest extends PHPUnit_Framework_TestCase
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
        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

        $factory = new \Application\Application\Specification\Zend\ZendSpecificationFactory($em);
        $spec = $factory->getDepartmentSpecification();
        $subject = array(
            "companyId" => 1,
            "departmentId" => 23
        );
        var_dump($spec->isSatisfiedBy($subject));
    }
}
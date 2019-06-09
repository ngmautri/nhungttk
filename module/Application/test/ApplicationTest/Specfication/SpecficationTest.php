<?php
namespace ApplicationTest\Model;

use Ramsey\Uuid\Uuid;
use PHPUnit_Framework_TestCase;
use Doctrine\ORM\EntityManager;
use ApplicationTest\Bootstrap;

class SpecficationTest extends PHPUnit_Framework_TestCase
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
        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

         $factory = new \Application\Application\Specification\Zend\ZendSpecificationFactory($em);
        $spec = $factory->getCostCenterExitsSpecification();
        $spec->setCompanyId(1);
        var_dump($spec->isSatisfiedBy(-6)); 
        
        
    }
}
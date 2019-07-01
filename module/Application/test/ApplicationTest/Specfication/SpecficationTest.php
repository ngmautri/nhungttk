<?php
namespace ApplicationTest\Model;

use ApplicationTest\Bootstrap;
use Doctrine\ORM\EntityManager;
use User\Infrastructure\Persistence\DoctrineUserRepository;
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
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

        $factory = new \Application\Application\Specification\Zend\ZendSpecificationFactory($em);
        $spec = $factory->getWarehouseACLSpecification();
        $subject = array(
            "companyId" => 1,
            "warehouseId" => 6,
            "userId" => 46,
        );
        var_dump($spec->isSatisfiedBy($subject)); 
    }
}
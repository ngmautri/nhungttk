<?php
namespace HRTest\Specification;

use Doctrine\ORM\EntityManager;
use HR\Application\Specification\Zend\IndividualSpecificationFactoryImpl;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class SpecsTest extends PHPUnit_Framework_TestCase
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
        try {

            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $factory = new IndividualSpecificationFactoryImpl($doctrineEM);
            $spec = $factory->getEmployeeCodeExitsSpecification();
            $subject = array(
                "companyId" => 1,
                "employeeCode" => '6000'
            );
            var_dump($spec->isSatisfiedBy($subject));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
<?php
namespace ProcureTest\Specification;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Specification\Zend\ProcureSpecificationFactory;
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

            $factory = new ProcureSpecificationFactory($doctrineEM);
            $spec = $factory->getPrRowSpecification();
            $subject = array(
                "warehouseId" => 6,
                "prRowId" => 9100
            );
            var_dump($spec->isSatisfiedBy($subject));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
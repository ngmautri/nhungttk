<?php
namespace ApplicaionTest\UomTest\Service;

use Application\Application\Service\Uom\UomService;
use Application\Domain\Shared\Uom\Uom;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class UomServiceTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            /**
             *
             * @var UomService $uomService ;
             */
            $uomService = Bootstrap::getServiceManager()->get(UomService::class);
            $uom = new Uom("kg");
            $uomService->add($uom);

            $uom = new Uom("kg");
            $uomService->add($uom);

            $result = $uomService->getUomCollecion();
            \var_dump(count($result));


        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
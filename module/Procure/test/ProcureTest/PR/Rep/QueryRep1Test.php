<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Domain\Doctrine\PRQueryRepositoryImplV1;
use PHPUnit_Framework_TestCase;

class QueryRep1Test extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new PRQueryRepositoryImplV1($doctrineEM);

            $id = 1165;
            $token = "e35fCqL7Be_JewXNMm_fZsseg93ehgYN";

            $id = 1454;
            $token = "4a600b5e-d6bc-43be-86c6-978308aaf746";

            $rootEntity = $rep->getRootEntityByTokenId($id, $token);
            var_dump($rootEntity->getRowsGenerator()->current());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
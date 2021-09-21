<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Doctrine\PRQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class RepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new PRQueryRepositoryImpl($doctrineEM);

            $id = 1165;
            $token = "e35fCqL7Be_JewXNMm_fZsseg93ehgYN";

            $id = 1435;
            $token = "bea192eb-cadb-47a0-8f8f-443dea3b0176";

            $rootEntity = $rep->getRootEntityByTokenId($id, $token);
            var_dump($rootEntity->getCompletedRows());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
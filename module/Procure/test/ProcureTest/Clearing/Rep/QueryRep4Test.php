<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Domain\Doctrine\PRQueryRepositoryImplV1;
use PHPUnit_Framework_TestCase;

class QueryRep4Test extends PHPUnit_Framework_TestCase
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

            $id = 1454;
            $token = "4a600b5e-d6bc-43be-86c6-978308aaf746";

            $result = $rep->getRootEntityByTokenId($id, $token);
            $result->refreshDoc();
            var_dump($result->getRefreshed());
            var_dump($result->getGeneratorInjected());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
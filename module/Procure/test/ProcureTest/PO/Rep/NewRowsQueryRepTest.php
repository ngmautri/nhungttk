<?php
namespace ProcureTest\PO\Rep;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Domain\Doctrine\POQueryRepositoryImplV1;
use PHPUnit_Framework_TestCase;

class NewRowsQueryRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new POQueryRepositoryImplV1($doctrineEM);

            $rootEntityId = 647;
            $rootEntityToken = "d9eb59d0-2cd7-494d-927a-bcb264a39057";

            $rootEntity = $rep->getRootEntityByTokenId($rootEntityId, $rootEntityToken);
            $rootEntity->refreshDoc();

            var_dump($rootEntity->getRefreshed());
            var_dump($rootEntity->getBilledAmount());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
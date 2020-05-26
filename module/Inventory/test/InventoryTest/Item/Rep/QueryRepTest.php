<?php
namespace InventoryTest\Item\Rep;

use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Doctrine\ItemQueryRepositoryImpl;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class QueryRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new ItemQueryRepositoryImpl($doctrineEM);

            $id = 5071;
            $token = "d0f492e0-04ec-4d1f-9b01-9db637f928f7";
            $rootEntity = $rep->getRootEntityByTokenId($id, $token);
            var_dump($rep->getVersion($id));

            var_dump($rootEntity->makeSnapshot());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
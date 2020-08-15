<?php
namespace InventoryTest\Warehouse\Rep;

use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Doctrine\WhQueryRepositoryImpl;
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

            $rep = new WhQueryRepositoryImpl($doctrineEM);

            $id = 22;
            $token = "5e5a3492-bf88-49d8-9bc2-678dc74766f8";

            $rootEntity = $rep->getByTokenId($id, $token);
            \var_dump($rootEntity->getScrapLocation());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
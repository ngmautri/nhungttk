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

            $id = 8;
            $token = "AfsVOwbqb1_4uyY7BOuIAwCXlO29SoTL";

            $rootEntity = $rep->getByTokenId($id, $token);
            \var_dump($rootEntity->lastChangeBy);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
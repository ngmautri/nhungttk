<?php
namespace InventoryTest\Item\Rep;

use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;
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

            $rep = new TrxQueryRepositoryImpl($doctrineEM);

            $id = 1362;
            $token = "af6d3b5b-e838-479e-b989-3c15142ba37c";

            $rootEntity = $rep->getRootEntityByTokenId($id, $token);
            \var_dump($rootEntity);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
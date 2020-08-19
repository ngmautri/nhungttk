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

            $id = 1415;
            $token = "53c733c3-f9c4-411d-90f6-7ea596b4bf26";

            $rootEntity = $rep->getRootEntityByTokenId($id, $token);

            $before = memory_get_usage();
            $o1 = clone $rootEntity;
            $after = memory_get_usage();
            \var_dump($after - $before);

            \var_dump($o1->getSysNumber());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
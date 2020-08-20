<?php
namespace InventoryTest\Item\Rep;

use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Symfony\Component\Stopwatch\Stopwatch;
use PHPUnit_Framework_TestCase;

class QueryRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            $stopWatch = new Stopwatch();
            $stopWatch->start("test");

            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new TrxQueryRepositoryImpl($doctrineEM);

            $id = 1415;
            $token = "53c733c3-f9c4-411d-90f6-7ea596b4bf26";

            $rootEntity = $rep->getLazyRootEntityByTokenId($id, $token);
            $timer = $stopWatch->stop("test");
            echo $timer . "===\n";

            $stopWatch->start("test");
            \var_dump($rootEntity->getLazyRowsCollection()->next());
            $timer = $stopWatch->stop("test");
            echo $timer . "===\n";
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
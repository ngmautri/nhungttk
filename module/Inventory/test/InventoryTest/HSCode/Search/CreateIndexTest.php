<?php
namespace InventoryTest\HSCode\Search;

use Doctrine\ORM\EntityManager;
use Inventory\Application\Service\Search\ZendSearch\HSCode\HSCodeSearchIndexImpl;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Symfony\Component\Stopwatch\Stopwatch;
use PHPUnit_Framework_TestCase;

class CreateIndexTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $stopWatch = new Stopwatch();
            $stopWatch->start("test");

            // $indexer = new HSCodeSearchIndexImpl();

            $indexer = Bootstrap::getServiceManager()->get(HSCodeSearchIndexImpl::class);

            // $r = $indexer->createIndex(null);
            $r = $indexer->optimizeIndex();
            var_dump($r);

            $timer = $stopWatch->stop("test");
            echo $timer;
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
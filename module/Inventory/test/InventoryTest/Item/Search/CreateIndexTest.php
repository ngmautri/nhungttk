<?php
namespace InventoryTest\Item\Search;

use Doctrine\ORM\EntityManager;
use Inventory\Application\Service\Search\ZendSearch\Item\ItemSearchIndexImplV1;
use Inventory\Infrastructure\Persistence\Doctrine\ItemReportRepositoryImpl;
use Inventory\Infrastructure\Persistence\Filter\ItemReportSqlFilter;
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
            $logger = Bootstrap::getServiceManager()->get('AppLogger');

            $stopWatch = new Stopwatch();
            $rep = new ItemReportRepositoryImpl($doctrineEM);

            $filter = new ItemReportSqlFilter();
            $filter->setIsActive(1);
            $sort = null;
            $sort_by = null;
            $limit = null;
            $offset = null;

            $stopWatch->start("test");
            $results = $rep->getItemListForIndexing($filter, $sort_by, $sort, $limit, $offset);
            // var_dump(\get_class($results->current()));

            $indexer = new ItemSearchIndexImplV1();
            $indexer->setLogger($logger);

            $r = $indexer->createNewIndex($results);
            var_dump($r);
            $r = $indexer->optimizeIndex();
            var_dump($r);

            $timer = $stopWatch->stop("test");
            echo $timer;
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
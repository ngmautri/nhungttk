<?php
namespace InventoryTest\Item\Search;

use Doctrine\ORM\EntityManager;
use Inventory\Application\Service\Search\ZendSearch\Item\ItemSearchIndexImpl;
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

            $stopWatch = new Stopwatch();
            $rep = new ItemReportRepositoryImpl($doctrineEM);

            $filter = new ItemReportSqlFilter();
            $filter->setIsActive(1);
            $sort_by = null;
            $sort = null;
            $limit = null;
            $offset = null;

            $stopWatch->start("test");

            $results = $rep->getItemList($filter, $sort_by, $sort, $limit, $offset);

            $indexer = new ItemSearchIndexImpl();
            // $r = $indexer->createIndex($results);
            $r = $indexer->optimizeIndex();
            var_dump($r);

            $timer = $stopWatch->stop("test");
            echo $timer;
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
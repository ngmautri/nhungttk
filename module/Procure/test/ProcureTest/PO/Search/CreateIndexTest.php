<?php
namespace ProcureTest\PO\Search;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Service\Search\ZendSearch\PO\PoSearchIndexImpl;
use Procure\Application\Service\Search\ZendSearch\PO\PoSearchQueryImpl;
use Procure\Application\Service\Search\ZendSearch\PO\Filter\PoQueryFilter;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\PORowSnapshotAssembler;
use Procure\Infrastructure\Persistence\Doctrine\PoReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Filter\PoReportSqlFilter;
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
            $rep = new PoReportRepositoryImpl($doctrineEM);
            $sort_by = Null;
            $sort = null;
            $limit = 100;
            $offset = 0;
            $filter = new PoReportSqlFilter();
            $filter->setIsActive(1);
            $stopWatch->start("test");
            $results = $rep->getAllRow($filter, $sort_by, $sort, $limit, $offset);

            $indexer = new PoSearchIndexImpl();
            // $r = $indexer->createIndex($results);
            // var_dump($r);

            $searcher = new PoSearchQueryImpl();
            $queryFilter = new PoQueryFilter();
            // $queryFilter->setDocStatus("posted");
            $hits = $searcher->search("40101423", $queryFilter);

            var_dump($hits->getTotalHits());

            $list = [];
            foreach ($hits->getHits() as $hit) {
                $list[] = PORowSnapshotAssembler::createFromQueryHit($hit);
            }

            var_dump($list);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
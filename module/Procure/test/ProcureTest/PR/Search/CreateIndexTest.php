<?php
namespace ProcureTest\PR\Search;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Service\Search\ZendSearch\PR\PrSearchIndexImpl;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Doctrine\PrReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Filter\PrReportSqlFilter;
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
            $rep = new PrReportRepositoryImpl($doctrineEM);
            $sort_by = Null;
            $sort = null;
            $limit = null;
            $offset = null;
            $filter = new PrReportSqlFilter();
            $filter->setBalance(- 1); // all.
            $filter->setIsActive(1);
            $stopWatch->start("test");
            $results = $rep->getAllRow($filter, $sort_by, $sort, $limit, $offset);

            $indexer = new PrSearchIndexImpl();
            $r = $indexer->createIndex($results);
            var_dump($r);
            $r = $indexer->optimizeIndex();
            var_dump($r);

            // $searcher = new PoSearchQueryImpl();
            // $queryFilter = new PoQueryFilter();
            // $queryFilter->setDocStatus("posted");
            // $hits = $searcher->search("40101423", $queryFilter);

            // var_dump($hits->getTotalHits());

            /*
             * $list = [];
             * foreach ($hits->getHits() as $hit) {
             * $list[] = PORowSnapshotAssembler::createFromQueryHit($hit);
             * }
             *
             * var_dump($list);
             */

            /*
             * $string = "Bàn ấn (chân vịt) bằng thép (Linh kiện
             * của máy may công
             * nghiệp)/277196000009 . Hàng mới
             * 100%";
             */
            /*
             * $string = "Tách dầu";
             *
             * $output = iconv("UTF-8", "ASCII//IGNORE", $string);
             */
            // $clean = iconv('UTF-8', 'ASCII//TRANSLIT', utf8_encode($s));
            // var_dump($output);

            $timer = $stopWatch->stop("test");
            echo $timer;
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
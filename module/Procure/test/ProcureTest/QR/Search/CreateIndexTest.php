<?php
namespace ProcureTest\QR\Search;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Service\Search\ZendSearch\QR\QrSearchIndexImpl;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Doctrine\QrReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Filter\QrReportSqlFilter;
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
            $rep = new QrReportRepositoryImpl($doctrineEM);
            $sort_by = Null;
            $sort = null;
            $limit = null;
            $offset = null;
            $filter = new QrReportSqlFilter();
            $filter->setIsActive(1);
            $stopWatch->start("test");
            $results = $rep->getAllRow($filter, $sort_by, $sort, $limit, $offset);

            $indexer = new QrSearchIndexImpl();
            $r = $indexer->createIndex($results);
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
            $string = "Tách dầu";

            $output = iconv("UTF-8", "ASCII//IGNORE", $string);

            // $clean = iconv('UTF-8', 'ASCII//TRANSLIT', utf8_encode($s));
            var_dump($output);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
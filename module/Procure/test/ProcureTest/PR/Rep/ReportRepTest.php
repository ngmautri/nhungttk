<?php
namespace ProcureTest\GR\Command;

use DoctrineORMModule\Options\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Reporting\PR\PrReporter;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Doctrine\PrReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Filter\PrReportSqlFilter;
use Procure\Infrastructure\Persistence\Helper\PrReportHelper;
use Symfony\Component\Stopwatch\Stopwatch;
use PHPUnit_Framework_TestCase;

class RepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            $cache = Bootstrap::getServiceManager()->get('AppCache');
            $report = Bootstrap::getServiceManager()->get(PrReporter::class);

            $rep = new PrReportRepositoryImpl($doctrineEM);
            $stopWatch = new Stopwatch();

            $filter = new PrReportSqlFilter();
            $filter->setBalance(1);
            $filter->setIsActive(1);
            $filter->setItemId(3514);

            $sort_by = Null;
            $sort = null;
            $limit = null;
            $offset = null;
            $stopWatch->start("test");
            // $result = $rep->getAllRowTotal($filter);
            // $result1 = $rep->getAllRow($filter, $sort_by, $sort, $limit, $offset);
            $result1 = $report->getAllRow($filter, $sort_by, $sort, $limit, $offset, null);
            // $results = PrReportHelper::getAllRow($doctrineEM, $filter, $sort_by, $sort, $limit, $offset);

            // $total = PrReportHelper::getAllRowTotal($doctrineEM, $filter);

            $timer = $stopWatch->stop("test");
            echo $timer;

            $key = \sprintf("test_total_rows_%s", $filter->__toString());

            /*
             * $resultCache = $cache->getItem($key);
             * if (! $resultCache->isHit()) {
             * $total = PrReportHelper::getAllRowTotal($doctrineEM, $filter);
             * $resultCache->set($total);
             * $cache->save($resultCache);
             * } else {
             * $total = $cache->getItem($key)->get();
             * echo \sprintf('\n cached: %s \n', $total);
             * }
             */

            // \var_dump($result);
            var_dump($result1);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
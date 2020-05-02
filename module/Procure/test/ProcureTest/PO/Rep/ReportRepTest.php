<?php
namespace ProcureTest\PO\Rep;

use DoctrineORMModule\Options\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Doctrine\PoReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Filter\PoReportSqlFilter;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
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

            $rep = new PoReportRepositoryImpl($doctrineEM);
            $sort_by = Null;
            $sort = null;
            $limit = null;
            $offset = null;
            $filter = new PoReportSqlFilter();
            $filter->setBalance(1);
            $filter->setIsActive(1);
            $filter->setDocYear(2020);
            $result = $rep->getAllRow($filter, $sort_by, $sort, $limit, $offset);
            $key = "result_" . $filter->__toString();

            // $path = "C:\NMT\nmt-workspace\mla-2.6.8\data\cache";
            $path = __DIR__ . "./cache";
            $cachePool = new FilesystemAdapter('app', 10, $path);

            // 1. store string values
            $total = $cachePool->getItem($key);
            if (! $total->isHit()) {
                $total->set(($result));
                $cachePool->save($total);
            }

            if ($cachePool->hasItem($key)) {
                $total = $cachePool->getItem($key)->get();
            } else {
                $total = $rep->getAllRow($filter, $sort_by, $sort, $limit, $offset);
            }

            \var_dump($total);

            // var_dump($result[0]);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
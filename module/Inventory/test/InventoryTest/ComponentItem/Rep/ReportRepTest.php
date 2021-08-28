<?php
namespace InventoryTest\Transaction\Rep;

use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Persistence\Doctrine\TrxReportRepositoryImpl;
use Inventory\Infrastructure\Persistence\Filter\TrxRowReportSqlFilter;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class ReportRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new TrxReportRepositoryImpl($doctrineEM);

            // $filter = new TrxReportSqlFilter();
            $filter = new TrxRowReportSqlFilter();
            $filter->setIsActive(1);
            $filter->setItem(1010);
            $filter->setFromDate('2019-1-1');
            $sort_by = null;
            $sort = null;
            $limit = 1;
            $offset = 1;

            $result = $rep->getList($filter, $sort_by, $sort, $limit, $offset);
            \var_dump($result);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
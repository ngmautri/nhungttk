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
            $filter->setWarehouseId(0);
            $filter->setDocStatus("posted");
            $filter->setFromDate('2020-08-01');
            $filter->setToDate('2020-09-30');
            $sort_by = null;
            $sort = null;
            $limit = null;
            $offset = null;
            echo $filter;
            $result = $rep->getAllRow($filter, $sort_by, $sort, $limit, $offset);
            \var_dump(count($result));
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}

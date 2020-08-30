<?php
namespace InventoryTest\Transaction\Rep;

use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Persistence\Doctrine\TrxReportRepositoryImpl;
use Inventory\Infrastructure\Persistence\Filter\CostIssueForSqlFilter;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class CostIssueForRepTest extends PHPUnit_Framework_TestCase
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
            $filter = new CostIssueForSqlFilter();
            $filter->setIsActive(1);
            $filter->setIssueFor(270);
            $filter->setDocStatus("posted");
            $filter->setFromDate("2020-08-31");
            $filter->setToDate("2021-08-31");
            $sort_by = null;
            $sort = null;
            $offset = null;
            $limit = null;
            echo ($filter);

            $result = $rep->getAllRowIssueFor($filter, $sort_by, $sort, $limit, $offset);
            \var_dump(count($result));
            \var_dump($result);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}

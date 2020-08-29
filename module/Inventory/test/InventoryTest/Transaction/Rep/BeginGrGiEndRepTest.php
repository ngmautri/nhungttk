<?php
namespace InventoryTest\Transaction\Rep;

use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Persistence\Doctrine\TrxReportRepositoryImpl;
use Inventory\Infrastructure\Persistence\Filter\BeginGrGiEndSqlFilter;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class BeginGrGiEndRepTest extends PHPUnit_Framework_TestCase
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
            $filter = new BeginGrGiEndSqlFilter();
            $filter->setIsActive(1);
            $filter->setItemId(1010);
            $filter->setDocStatus("posted");
            $filter->setFromDate("2020-08-01");
            $filter->setToDate("2020-08-31");
            $sort_by = null;
            $sort = null;
            $offset = null;
            $limit = null;
            echo ($filter);

            $result = $rep->getBeginGrGiEnd($filter, $sort_by, $sort, $limit, $offset);
            \var_dump(count($result));
            \var_dump($result);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}

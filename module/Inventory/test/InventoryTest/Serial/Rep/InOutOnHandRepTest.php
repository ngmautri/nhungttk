<?php
namespace InventoryTest\Item\Rep;

use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Persistence\Doctrine\ItemTrxReportRepositoryImpl;
use Inventory\Infrastructure\Persistence\Filter\InOutOnhandSqlFilter;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class InOutOnHandRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new ItemTrxReportRepositoryImpl($doctrineEM);

            $filter = new InOutOnhandSqlFilter();
            $filter->setIsActive(1);
            $filter->setDocStatus("posted");
            $filter->setFromDate("2020-08-01");
            $filter->setToDate("2020-08-31");
            $sort_by = null;
            $sort = null;
            $offset = 1;
            $limit = 1;

            $result = $rep->getInOutOnhand($filter, $sort_by, $sort, $limit, $offset);

            var_dump($result);
            // var_dump($result[0]->getGrValue());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
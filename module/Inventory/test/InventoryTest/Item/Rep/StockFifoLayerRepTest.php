<?php
namespace InventoryTest\Item\Rep;

use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Persistence\Doctrine\StockReportRepositoryImpl;
use Inventory\Infrastructure\Persistence\Filter\StockFifoLayerReportSqlFilter;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class StockFifoLayerRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new StockReportRepositoryImpl($doctrineEM);
            $filter = new StockFifoLayerReportSqlFilter();
            $filter->setToDate('2020-06-06');
            $filter->setItemId(2427);
            $filter->setWarehouseId(5);
            $filter->setIsClosed(0);
            $sort_by = 'postingDate';
            $sort = 'DESC';
            $limit = null;
            $offset = null;

            $result = $rep->getFifoLayer($filter, $sort_by, $sort, $limit, $offset);
            var_dump(count($result));
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
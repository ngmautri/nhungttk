<?php
namespace InventoryTest\Item\Rep;

use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Persistence\Doctrine\StockReportRepositoryImpl;
use Inventory\Infrastructure\Persistence\Filter\StockOnhandReportSqlFilter;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class StockRepTest extends PHPUnit_Framework_TestCase
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
            $filter = new StockOnhandReportSqlFilter();
            $filter->setItemId(2427);
            $filter->setWarehouseId(5);
            $filter->setCheckingDate('2020-03-01');
            $result = $rep->getOnHandQuantity($filter);
            var_dump($result);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
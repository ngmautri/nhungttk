<?php
namespace InventoryTest\Item\Rep;

use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Persistence\Doctrine\ItemReportRepositoryImpl;
use Inventory\Infrastructure\Persistence\Filter\ItemReportSqlFilter;
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

            $rep = new ItemReportRepositoryImpl($doctrineEM);

            $filter = new ItemReportSqlFilter();
            $filter->setIsActive(- 1);
            $filter->setItemType(1);
            $sort_by = null;
            $sort = null;
            $limit = null;
            $offset = null;

            $result = $rep->getItemList($filter, $sort_by, $sort, $limit, $offset);
            echo count($result);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
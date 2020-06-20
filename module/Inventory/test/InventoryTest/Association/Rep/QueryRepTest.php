<?php
namespace InventoryTest\Association\Rep;

use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Persistence\Doctrine\AssociationQueryRepositoryImpl;
use Inventory\Infrastructure\Persistence\Filter\ItemReportSqlFilter;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class QueryRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new AssociationQueryRepositoryImpl($doctrineEM);

            $filter = new ItemReportSqlFilter();
            $filter->setIsActive(1);
            $sort_by = null;
            $sort = null;
            $limit = null;
            $offset = null;

            $result = $rep->getList($filter, $sort_by, $sort, $limit, $offset);
            \var_dump(count($result));
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
<?php
namespace InventoryTest\Association\Rep;

use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Doctrine\AssociationItemQueryRepositoryImpl;
use Inventory\Infrastructure\Persistence\Filter\AssociationSqlFilter;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class AssociationItemQueryRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new AssociationItemQueryRepositoryImpl($doctrineEM);

            $filter = new AssociationSqlFilter();
            $filter->setItemId(5099);
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
<?php
namespace InventoryTest\Item\Rep;

use Application\Infrastructure\Persistence\Doctrine\UomQueryRepositoryImpl;
use Application\Infrastructure\Persistence\Filter\UomSqlFilter;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class UomRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new UomQueryRepositoryImpl($doctrineEM);
            $filter = new UomSqlFilter();

            $sort_by = 'postingDate';
            $sort = 'DESC';
            $limit = null;
            $offset = null;

            $result = $rep->getList($filter, $sort_by, $sort, $limit, $offset);
            var_dump(count($result));
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
<?php
namespace InventoryTest\Item\Rep;

use Application\Infrastructure\Persistence\Doctrine\UomCrudRepositoryImpl;
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

            $rep = new UomCrudRepositoryImpl($doctrineEM);
            $filter = new UomSqlFilter();
            $filter->setSortBy('uomName');
            $filter->setLimit(1);

            $result = $rep->getList($filter);
            var_dump(($result->last()));
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
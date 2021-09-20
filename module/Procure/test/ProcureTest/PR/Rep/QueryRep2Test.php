<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Reporting\Doctrine\PrReportImplV1;
use Procure\Infrastructure\Persistence\SQL\Filter\PrRowReportSqlFilter;
use PHPUnit_Framework_TestCase;

class QueryRep2Test extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new PrReportImplV1($doctrineEM);

            $filter = new PrRowReportSqlFilter();
            $filter->setItemId(5954);
            $filter->setSortBy('prSubmitted');
            $filter->setSort('desc');
            $filter->setBalance(100);
            // $filter->setItemId(1010);
            $result = $rep->getAllRow($filter);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
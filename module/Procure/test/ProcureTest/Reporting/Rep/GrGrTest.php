<?php
namespace ProcureTest\Reporting\Rep;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Reporting\Doctrine\PrGrReportImpl;
use Procure\Infrastructure\Persistence\Reporting\Filter\PrGrReportSqlFilter;
use PHPUnit_Framework_TestCase;

class PoApTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new PrGrReportImpl($doctrineEM);
            $filter = new PrGrReportSqlFilter();
            // $filter->setFromDate('2020-01-04');
            // $filter->setToDate('2021-01-10');
            // $filter->setSortBy('ahre');
            $filter->setIsRowActive(1);

            // $filter->setOffset(1);
            // $filter->setLimit(10);
            $result = $rep->getList($filter);

            \var_dump($result->first());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
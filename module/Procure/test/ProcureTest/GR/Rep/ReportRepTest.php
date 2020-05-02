<?php
namespace ProcureTest\GR\Rep;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Doctrine\GrReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Filter\GrReportSqlFilter;
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

            $rep = new GrReportRepositoryImpl($doctrineEM);

            $isActive = 1;
            $balance = 1;
            $docYear = 2019;
            $docStatus = "posted";

            $filter = new GrReportSqlFilter();
            $filter->setIsActive($isActive);
            $filter->setBalance($balance);
            $filter->setDocYear($docYear);
            $filter->setDocStatus($docStatus);
            $sort_by = null;
            $sort = null;
            $limit = 1;
            $offset = 1;

            // var_dump($rep->getList($filter, $sort_by, $sort, $limit, $offset));
            var_dump($rep->getListTotal($filter));
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
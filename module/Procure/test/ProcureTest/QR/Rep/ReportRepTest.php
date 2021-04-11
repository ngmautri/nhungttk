<?php
namespace ProcureTest\QR\Rep;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Doctrine\QrReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Filter\QrReportSqlFilter;
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

            $rep = new QrReportRepositoryImpl($doctrineEM);
            $filter = new QrReportSqlFilter();
            $filter->setBalance(1);
            $filter->setIsActive(1);
            $filter->setVendorId(5);

            $result = $rep->getAllRow($filter);
            echo count($result);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
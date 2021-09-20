<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Reporting\Doctrine\PrReportImplV1;
use Procure\Infrastructure\Persistence\SQL\Filter\PrHeaderReportSqlFilter;
use Procure\Infrastructure\Persistence\SQL\Filter\PrRowReportSqlFilter;
use PHPUnit_Framework_TestCase;

class QueryRep3Test extends PHPUnit_Framework_TestCase
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

            $filterHeader = new PrHeaderReportSqlFilter();
            $filterHeader->setDocYear(2021);
            $filterHeader->setBalance(2);

            $filterRows = new PrRowReportSqlFilter();
            $filterRows->setDocYear(2021);
            $filterRows->setBalance(2);
            $result = $rep->getListTotal($filterHeader, $filterRows);
            //
            var_dump($result);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
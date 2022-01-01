<?php
namespace ProcureTest\PR\Service;

use ProcureTest\Bootstrap;
use Procure\Application\Reporting\PR\PrReporter;
use Procure\Application\Reporting\PR\PrReporterV1;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\SQL\Filter\PrHeaderReportSqlFilter;
use Procure\Infrastructure\Persistence\SQL\Filter\PrRowReportSqlFilter;
use PHPUnit_Framework_TestCase;

class PrReporterV1Test extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var PrReporterV1 $reporter ; */

            $reporter = Bootstrap::getServiceManager()->get(PrReporter::class);

            $filterHeader = new PrHeaderReportSqlFilter();
            $filterHeader->setDocYear(2021);
            $filterHeader->setBalance(1);
            $filterHeader->setOffset(1);
            $filterHeader->setLimit(10);

            $filterRows = new PrRowReportSqlFilter();
            $filterRows->setDocYear(2021);
            $filterRows->setBalance(100);

            $page = 1;
            $render = $reporter->getHeaderCollectionRender($filterHeader, $filterRows, $page);
            var_dump($render->execute());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
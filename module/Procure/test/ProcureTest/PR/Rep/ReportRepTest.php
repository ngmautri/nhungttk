<?php
namespace ProcureTest\GR\Command;

use DoctrineORMModule\Options\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Doctrine\PrReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Filter\PrReportSqlFilter;
use PHPUnit_Framework_TestCase;

class RepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new PrReportRepositoryImpl($doctrineEM);

            $filter = new PrReportSqlFilter();
            $filter->setBalance(1);
            $filter->setIsActive(1);
            $filter->setPrYear(2020);
            $result = $rep->getAllRowTotal($filter);

            \var_dump($result);
            // var_dump($result[1]);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
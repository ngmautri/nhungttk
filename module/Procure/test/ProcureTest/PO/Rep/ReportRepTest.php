<?php
namespace ProcureTest\PO\Rep;

use DoctrineORMModule\Options\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Doctrine\PoReportRepositoryImpl;
use Procure\Infrastructure\Persistence\Filter\PoReportSqlFilter;
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

            $rep = new PoReportRepositoryImpl($doctrineEM);
            $sort_by = Null;
            $sort = null;
            $filter = new PoReportSqlFilter();
            $filter->setBalance(1);
            $filter->setIsActive(1);
            $filter->setDocYear(2020);
            $result = $rep->getAllRowTotal($filter);

            \var_dump($result);
            // var_dump($result[0]);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
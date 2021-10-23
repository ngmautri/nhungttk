<?php
namespace ProcureTest\PO\Rep;

use DoctrineORMModule\Options\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Domain\Doctrine\Helper\PoHeaderHelper;
use Procure\Infrastructure\Persistence\SQL\Filter\PoHeaderReportSqlFilter;
use Symfony\Component\Stopwatch\Stopwatch;
use PHPUnit_Framework_TestCase;

class DocMapReportRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $stopWatch = new Stopwatch();
            $stopWatch->start("test");

            $filter = new PoHeaderReportSqlFilter();
            $filter->setPoId(624);

            $result = PoHeaderHelper::getDocmapFor($doctrineEM, $filter);
            echo (count($result));

            $timer = $stopWatch->stop("test");
            echo $timer;
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
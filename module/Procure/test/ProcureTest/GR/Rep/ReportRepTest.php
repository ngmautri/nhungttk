<?php
namespace ProcureTest\GR\Rep;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Doctrine\GrReportRepositoryImpl;
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

            var_dump($rep->getListTotal());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
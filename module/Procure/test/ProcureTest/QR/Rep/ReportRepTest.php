<?php
namespace ProcureTest\QR\Rep;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Doctrine\QrReportRepositoryImpl;
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

            $id = 1165;
            $token = "e35fCqL7Be_JewXNMm_fZsseg93ehgYN";

            $id = 1123;
            $token = "kKXsCBJre__Re87TdMH6tyZH_T7FatqR";

            $result = $rep->getListTotal();
            var_dump($result);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
<?php
namespace ProcureTest\PO\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;

class QueryRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new POQueryRepositoryImpl($doctrineEM);

            $id = 447;
            $token = "b4b321c1-9e40-4aba-87eb-80c03f182623";

            $rootEntity = $rep->getPODetailsById($id, $token);
            $row = $rootEntity->getDocRows()[0];
            var_dump($row->calculatePriceAndQuanity());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
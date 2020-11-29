<?php
namespace ProcureTest\PO\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

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

            $rootEntityId = 485;
            $rootEntityToken = "0fee41a0-d63e-4de8-a98c-e3be586dca58";

            $rootEntity = $rep->getPODetailsById($rootEntityId, $rootEntityToken);
            // $row = $rootEntity->getDocRows()[0];
            // var_dump($row->calculatePriceAndQuanity());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
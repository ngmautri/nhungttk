<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\RowSnapshot;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class QueryRep1Test extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new GRQueryRepositoryImpl($doctrineEM);

            $id = 595;
            $token = "15d1b083-2b8d-40b6-9c56-b0152dc30013";
            $rootEntity = $rep->getRootEntityByTokenId($id, $token);

            foreach ($rootEntity->getDocRows() as $doc) {
                echo $doc->getWarehouse() . "\n";
            }

            $rootEntity->sortRowsByWarehouse();

            echo "========\n";
            foreach ($rootEntity->getDocRows() as $doc) {
                echo \sprintf("%s-  %s \n", $doc->getWarehouse(), $doc->getItemName());
            }
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
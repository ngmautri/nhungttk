<?php
namespace ProcureTest\Ap\Rep;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
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

            $rep = new APQueryRepositoryImpl($doctrineEM);

            $id = 3298;
            $token = "793ab28b-95e5-42ec-98b4-93c1426c9080";

            $rootEntity = $rep->getRootEntityByTokenId($id, $token);

            foreach ($rootEntity->getDocRows() as $doc) {
                // echo \sprintf("%s- %s \n", $doc->getWarehouse(), $doc->getItemName());
            }

            $rootEntity->sortRowsByWarehouse();

            echo "========\n";
            foreach ($rootEntity->getDocRows() as $doc) {
                // echo \sprintf("%s- %s \n", $doc->getWarehouse(), $doc->getItemName());
            }

            $results = $rootEntity->generateDocumentByWarehouse();
            echo "========\n";

            echo count($results);
            echo "=\n========\n";

            // \var_dump($results[0]);
            foreach ($results as $doc) {
                echo \sprintf("%s %s %s %s // %s \n", $doc->getDocType(), $doc->getRemarks(), $doc->getTotalRows(), $doc->getUuid(), \spl_object_hash($doc));
            }
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
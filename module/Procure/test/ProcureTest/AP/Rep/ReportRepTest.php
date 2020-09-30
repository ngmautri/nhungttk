<?php
namespace ProcureTest\Ap\Rep;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\AccountPayable\APDoc;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;
use Procure\Domain\AccountPayable\GenericAP;

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

            $id = 3366;
            $token = "e110b32a-7b70-4575-a0c4-937bacb878cf";

            /**
             *
             * @var GenericAP $rootEntity ;
             */
            $rootEntity = $rep->getRootEntityByTokenId($id, $token);

            $results = $rootEntity->generateDocumentByWarehouse();

            // \var_dump($results[0]);
            foreach ($results as $doc) {

                echo \sprintf("\n%s %s %s %s // %s ", $doc->getDocType(), "", $doc->getDocRowsCount(), $doc->getUuid(), \spl_object_hash($doc));
                foreach ($doc->getDocRows() as $row) {
                    echo "\n" . $row->getItemName();
                }
            }
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
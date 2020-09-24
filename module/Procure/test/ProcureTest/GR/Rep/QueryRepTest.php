<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\RowSnapshot;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;
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

            $rep = new GRQueryRepositoryImpl($doctrineEM);

            $id = 595;
            $token = "15d1b083-2b8d-40b6-9c56-b0152dc30013";
            $rootEntity = $rep->getLazyRootEntityById($id);
            // $c = $rootEntity->getLazyRowSnapshotCollection()->current();
            // var_dump($c());
            // var_dump(count($rootEntity->slipByWarehouse()));
            // var_dump(($rootEntity->slipByWarehouse()[5]));

            $results = $rootEntity->slipRowsByWarehouse();

            foreach ($results as $k => $v) {

                foreach ($v as $rowSnapshot) {

                /**
                 *
                 * @var RowSnapshot $rowSnapshot
                 */
                    // echo \sprintf("%s=>%s, %s \n", $k, $rowSnapshot->getItem(), $rowSnapshot->getItemName());
                }
            }

            $results = $rootEntity->createSubDocumentByWarehouse();

            foreach ($results as $doc) {

                echo \sprintf("%s;%s-WH%s, %s\n", $doc->getDocType(), $doc->getSysNumber(), $doc->getWarehouse(), $doc->getRowSnapshotCollection()->count());
            }
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
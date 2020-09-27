<?php
namespace InventoryTest\Item\Rep;

use Doctrine\ORM\EntityManager;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Symfony\Component\Stopwatch\Stopwatch;
use PHPUnit_Framework_TestCase;

class QueryRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            $stopWatch = new Stopwatch();
            $stopWatch->start("test");

            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new TrxQueryRepositoryImpl($doctrineEM);

            /*
             * $id = 1416;
             * $token = "1623f80f-c267-4d10-b2f4-0f908a0a2229";
             */
            $id = 1572;
            $token = "ee1d2ecc-98d2-4a56-b7a6-ff11e36fa9bd";

            $rootEntity = $rep->getDetailLazyRootEntityByTokenId($id, $token);
            var_dump($rootEntity->getTotalRows());
            var_dump($rootEntity->getUnusedRows());
            var_dump($rootEntity->getMovementFlow());
            var_dump($rootEntity->getTransactionStatus());

            $timer = $stopWatch->stop("test");
            echo $timer . "===\n";

            $stopWatch->start("test");
            // $lazyRows = $rootEntity->getLazyRowsCollection();
            $r = $rootEntity->getLazyRowSnapshotCollection()->slice(300, 3);
            foreach ($r as $lazyRow) {
                \var_dump($lazyRow()->getItemName());
            }

            $timer = $stopWatch->stop("test");
            echo $timer . "===\n";
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
<?php
namespace InventoryTest\Item\Service;

use Doctrine\ORM\EntityManager;
use Inventory\Application\Service\Item\FIFOServiceImpl;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Symfony\Component\Stopwatch\Stopwatch;
use PHPUnit_Framework_TestCase;

class FifoLayerServiceTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            $logger = Bootstrap::getServiceManager()->get('AppLogger');

            $stopWatch = new Stopwatch();
            $stopWatch->start("test");

            $rep = new TrxQueryRepositoryImpl($doctrineEM);

            $id = 1362;
            $token = "af6d3b5b-e838-479e-b989-3c15142ba37c";

            $rootEntity = $rep->getRootEntityByTokenId($id, $token);

            $fifoService = new FIFOServiceImpl();
            $fifoService->setDoctrineEM($doctrineEM);
            $fifoService->setLogger($logger);

            $cost = $fifoService->calculateCostOfTrx($rootEntity);
            echo $cost;

            $timer = $stopWatch->stop("test");
            echo "\n============\n" . $timer;
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
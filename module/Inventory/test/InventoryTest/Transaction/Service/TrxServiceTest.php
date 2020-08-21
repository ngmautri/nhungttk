<?php
namespace InventoryTes\Transaction;

use Inventory\Application\Export\Transaction\Contracts\SaveAsSupportedType;
use Inventory\Application\Service\Transaction\TrxService;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Symfony\Component\Stopwatch\Stopwatch;
use PHPUnit_Framework_TestCase;

class TrxServiceTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            $stopWatch = new Stopwatch();
            $stopWatch->start("test");

            /** @var TrxService $sv ; */
            $sv = Bootstrap::getServiceManager()->get(TrxService::class);

            $id = 1415;
            $token = "53c733c3-f9c4-411d-90f6-7ea596b4bf26";

            $rootEntity = $sv->getLazyDocOutputByTokenId($id, $token, 1, 100, SaveAsSupportedType::OUTPUT_IN_ARRAY);
            // $output = $rootEntity->getRowsOutput();

            $timer = $stopWatch->stop("test");
            echo $timer . "===\n";
        } catch (InvalidArgumentException $e) {
            echo ($e->getMessage());
        }
    }
}
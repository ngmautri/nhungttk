<?php
namespace InventoryTes\Transaction;

use Inventory\Application\Service\Transaction\TrxService;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class TrxServiceTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            /** @var TrxService $sv ; */
            $sv = Bootstrap::getServiceManager()->get(TrxService::class);

            $id = 1317;
            $token = "1c80354d-b4e2-46be-a8c1-17a885e6d35f";

            $rootEntity = $sv->getDocDetailsByTokenId($id, $token);
            var_dump($rootEntity->getRowsOutput());
        } catch (InvalidArgumentException $e) {
            echo ($e->getMessage());
        }
    }
}
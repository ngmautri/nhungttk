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

            $id = 1362;
            $token = "af6d3b5b-e838-479e-b989-3c15142ba37c";

            $rootEntity = $sv->getDocDetailsByTokenId($id, $token);
            var_dump($rootEntity);
        } catch (InvalidArgumentException $e) {
            echo ($e->getMessage());
        }
    }
}
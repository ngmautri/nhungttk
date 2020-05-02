<?php
namespace ProcureTest\GR\Service;

use ProcureTest\Bootstrap;
use Procure\Application\Service\GR\GRService;
use Procure\Application\Service\PR\PRService;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class ServiceTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            /** @var PRService $sv ; */
            $sv = Bootstrap::getServiceManager()->get(GRService::class);

            $id = 120;
            $token = "1fb260d1-5762-4897-a6b4-975a5eb2bac8";

            $rootEntity = $sv->getDocDetailsByTokenId($id, $token);
            var_dump($rootEntity);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
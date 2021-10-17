<?php
namespace ProcureTest\PR;

use ProcureTest\Bootstrap;
use Procure\Application\Service\PR\PRServiceV1;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class PrService1Test extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            /** @var PRServiceV1 $sv ; */
            $sv = Bootstrap::getServiceManager()->get('Procure\Application\Service\PR\PRService');

            $id = 1454;
            $token = "4a600b5e-d6bc-43be-86c6-978308aaf746";

            $rootEntity = $sv->getProcureDocByTokenId($id, $token, 1, 2);
            var_dump($rootEntity->getRowsOutput());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
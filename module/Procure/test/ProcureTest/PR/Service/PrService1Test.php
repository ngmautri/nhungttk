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

            $id = 1450;
            $token = "6057d472-8a03-44f9-8460-3545fe99e451";

            $rootEntity = $sv->getDocDetailsByTokenId($id, $token, 1, 2);
            var_dump($rootEntity->getRowsOutput());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
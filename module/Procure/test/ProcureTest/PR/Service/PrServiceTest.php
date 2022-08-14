<?php
namespace ProcureTest\PR;

use ProcureTest\Bootstrap;
use Procure\Application\Service\PR\PRService;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class PrServiceTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
        // echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {

            /** @var PRService $sv ; */
            $sv = Bootstrap::getServiceManager()->get(PRService::class);

            $id = 1165;
            $token = "fdcc6590-801b-4ee4-985e-caf1abefbaf7";

            $id = 1123;
            $token = "kKXsCBJre__Re87TdMH6tyZH_T7FatqR";

            $rootEntity = $sv->getDocDetailsByTokenId($id, $token);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
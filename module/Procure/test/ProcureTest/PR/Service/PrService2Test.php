<?php
namespace Procure\test\ProcureTest\PR\Service;

use ProcureTest\Bootstrap;
use Procure\Application\Service\PR\PRServiceV2;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\SQL\Filter\PrRowReportSqlFilter;
use PHPUnit_Framework_TestCase;

class PrService2Test extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            /** @var PRServiceV2 $sv ; */
            $sv = Bootstrap::getServiceManager()->get('Procure\Application\Service\PR\PRService');

            $id = 1459;
            $token = "bea38e13-82a8-405b-90d2-751abaf3093c";

            $rootEntity = $sv->getDocDetailsByTokenId($id, $token);
            // var_dump($rootEntity->getRowCollection()->current());
            $filter = new PrRowReportSqlFilter();

            $page = 1;
            $render = $sv->getRowCollectionRender($rootEntity, $filter, $page);
            var_dump($render->execute());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
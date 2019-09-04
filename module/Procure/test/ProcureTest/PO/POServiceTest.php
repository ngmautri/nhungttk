<?php
namespace ProcureTest\PO;

use ProcureTest\Bootstrap;
use PHPUnit_Framework_TestCase;

use Procure\Application\DTO\Po\Output\PORowOutputStrategy;
use Procure\Application\Service\PO\POService;
use Procure\Domain\Exception\InvalidArgumentException;

class POServiceTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        // echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {

            /** @var POService $sv ; */
            $sv = Bootstrap::getServiceManager()->get('Procure\Application\Service\PO\POService');
            $results = $sv->getPODetailsById(199, PORowOutputStrategy::OUTPUT_IN_ARRAY);
            var_dump($results);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
<?php
namespace ApplicaionTest\CommonCollection\Service;

use Application\Application\Service\Shared\CommonCollection;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class CommonCollectionTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            /**
             *
             * @var CommonCollection $service ;
             */
            $service = Bootstrap::getServiceManager()->get(CommonCollection::class);
            $result = $service->getUomCollection();
            var_dump($result->current());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
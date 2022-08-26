<?php
namespace ApplicationTest\AccountChart\Service;

use Application\Application\Service\AccountChart\AccountChartService;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class AccountSearchQueryTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            /**
             *
             * @var AccountChartService $service ;
             */
            $service = Bootstrap::getServiceManager()->get(AccountChartService::class);

            $result = $service->getRootEntityById(13);
            var_dump($result->createChartTree()
                ->getRoot()
                ->display());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
<?php
namespace ApplicationTest\AccountChart\Rep;

use Application\Infrastructure\Persistence\Domain\Doctrine\ChartQueryRepositoryImpl;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class ChartQueryRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            $rep = new ChartQueryRepositoryImpl($doctrineEM);
            $result = $rep->getById(13);
            var_dump($result->createChartTree()->getRoot());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
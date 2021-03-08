<?php
namespace HGTest\Employee\Rep;

use Doctrine\ORM\EntityManager;
use HR\Infrastructure\Persistence\Domain\Doctrine\IndividualQueryRepositoryImpl;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class QueryRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testGetVersion()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new IndividualQueryRepositoryImpl($doctrineEM);
            $id = '3069';

            $result = $rep->getByEmployeeCode($id);
            var_dump($result);
        } catch (InvalidArgumentException $e) {
            // var_dump($e->getMessage());
        }
    }
}
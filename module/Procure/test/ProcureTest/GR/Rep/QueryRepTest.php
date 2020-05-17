<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class RepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new GRQueryRepositoryImpl($doctrineEM);

            $id = 94;
            $token = "cc15908b-e12f-4403-bbda-ceb5d824f1f5";
            $rootEntity = $rep->getRootEntityByTokenId($id, $token);
            var_dump($rootEntity->getDocRows());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
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

            $id = 589;
            $token = "dac6f3ee-df5b-4c46-87d5-fb13f5234087";
            $rootEntity = $rep->getRootEntityByTokenId($id, $token);
            var_dump($rootEntity->getDocType());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
<?php
namespace UserTest\Warehouse\Rep;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use User\Infrastructure\Doctrine\UserQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class QueryRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new UserQueryRepositoryImpl($doctrineEM);

            $id = 39;

            $rootEntity = $rep->getById($id);
            \var_dump($rootEntity);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
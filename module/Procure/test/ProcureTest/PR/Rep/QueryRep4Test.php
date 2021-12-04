<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\Domain\Doctrine\PRQueryRepositoryImplV1;
use PHPUnit_Framework_TestCase;

class QueryRep4Test extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new PRQueryRepositoryImplV1($doctrineEM);

            $id = 1459;
            $token = "bea38e13-82a8-405b-90d2-751abaf3093c";

            $result = $rep->getRootEntityByTokenId($id, $token);
            // $result->refreshDoc();
            var_dump($result->getRowCollection()->count());
            var_dump($result->getRowsGenerator()->valid());
            // var_dump($result->getRowsGenerator()->current());
            var_dump($result->getRowsGenerator()->valid());
            var_dump($result->getRowCollection()->count());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Doctrine\PRQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class RepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
        // echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new PRQueryRepositoryImpl($doctrineEM);

            $id = 1165;
            $token = "e35fCqL7Be_JewXNMm_fZsseg93ehgYN";
            $rootEntity = $rep->getRootEntityByTokenId($id, $token);
            var_dump($rootEntity->getDocRows());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
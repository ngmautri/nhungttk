<?php
namespace ProcureTest\Ap\Rep;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class QueryRepTest extends PHPUnit_Framework_TestCase
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
           
            $rep = new APQueryRepositoryImpl($doctrineEM);
            
            $id = 2814;
            $token="PdlU__QUEe_SJoYi4U4fu0Toe2MYY9mz";
            $rootEntity = $rep->getRootEntityByTokenId($id,$token);
            var_dump($rootEntity->getDocRows());
            
            
                        
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
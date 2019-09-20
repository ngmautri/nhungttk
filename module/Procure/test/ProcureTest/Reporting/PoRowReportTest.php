<?php
namespace ProcureTest\Reporting;

use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Procure\Infrastructure\Persistence\Doctrine\POListRepositoryImpl;


class PoRowReportTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;
 
    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        // echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {

            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            $rep = new POListRepositoryImpl($doctrineEM);
            $results = $rep->getAllPoRowStatus(1,2019,1, "", "", null, null);
            var_dump($results);
            
            
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
<?php
namespace ProcureTest\Reporting;

use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Procure\Infrastructure\Persistence\Doctrine\ItemReportRepostitoryImpl;

class ItemReportRepositoryTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

  
    protected $em;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {

            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            $rep = new ItemReportRepostitoryImpl($doctrineEM);
                        
            $result = $rep->getPriceOfItem(4570, null, "DECS", 2, 2);
            var_dump($result);
           
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
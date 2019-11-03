<?php
namespace ProcureTest\DTO;

use ProcureTest\Bootstrap;
use Procure\Application\Reporting\PR\PrRowStatusReporter;
use Procure\Application\Reporting\PR\Output\PrRowStatusOutputStrategy;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\DoctrinePRListRepository;
use PHPUnit_Framework_TestCase;

class PrRowStatusReportTest extends PHPUnit_Framework_TestCase
{
  
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
            $rep = new DoctrinePRListRepository();
            $rep->setDoctrineEM($doctrineEM);
            
        
            $reporter = new PrRowStatusReporter();
            $reporter->setPrListRespository($rep);
            $reporter->setDoctrineEM($doctrineEM);
            $results = $reporter->getPrStatus(835, 1, "itemName", "ASC",PrRowStatusOutputStrategy::OUTPUT_IN_ARRAY);
            var_dump($results);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
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

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
    protected $em;

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
            $results = $reporter->getPrRowStatus(1, 2019, 0, "itemName", "ASC",10,1,PrRowStatusOutputStrategy::OUTPUT_IN_HMTL_TABLE);
            var_dump($results);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
<?php
namespace ProcureTest\Reporting;

use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Procure\Infrastructure\Persistence\Doctrine\POListRepositoryImpl;
use Application\Domain\Util\ExcelColumnMap;
use Procure\Infrastructure\Persistence\Doctrine\APReportRepositoryImpl;
use Procure\Application\Reporting\AP\ApReporter;
use Procure\Application\Reporting\AP\Output\ApRowStatusOutputStrategy;

class APRowReportTest extends PHPUnit_Framework_TestCase
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
            $rep = new APReportRepositoryImpl($doctrineEM);
            
            /** @var ApReporter $reporter ; */
            
            $reporter = Bootstrap::getServiceManager()->get('Procure\Application\Reporting\AP\ApReporter');
            
            $vendor_id=null;
            $item_id =null;
            $is_active=null;
            $ap_year=2019;
            $ap_month=10;
            $balance=null;
            $sort_by=null;
            $sort=null;
            $limit=1;
            $offset=1;            
            $results = $reporter->getAllAPRowStatusTotal($vendor_id, $item_id, $is_active, $ap_year, $ap_month, $balance, $sort_by, $sort, $limit, $offset,ApRowStatusOutputStrategy::OUTPUT_IN_ARRAY);
            
            
            var_dump($results);
            
              
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
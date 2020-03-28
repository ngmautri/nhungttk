<?php
namespace ProcureTest\PO;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Reporting\PR\PrRowStatusReporter;
use Procure\Application\Reporting\PR\Output\PrRowStatusOutputStrategy;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Persistence\DoctrinePRListRepository;
use PHPUnit_Framework_TestCase;
use Procure\Infrastructure\Doctrine\DoctrinePOQueryRepository;
use Procure\Domain\PurchaseOrder\PORow;
use Procure\Domain\GoodsReceipt\GRRow;
use Procure\Domain\APInvoice\APDocRow;

class POQueryRepositoryTest extends PHPUnit_Framework_TestCase
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
            $rep = new DoctrinePOQueryRepository($doctrineEM);

            $result = $rep->getPODetailsById(338, "040b6a84-e217-4cfa-80e3-a9a9eb2c76ef");
            $rows = $result->getDocRows();
            foreach($rows  as $r){
                
                /**
                 * @var PORow $r ;
                 */
                
                $grRow = $r->convertTo(new APDocRow());
                var_dump($grRow);
            }
            
           
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
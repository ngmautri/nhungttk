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
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\GoodsReceipt\Factory\GRFactory;

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
            
            //$id = 302;
            //$token = "b69a9fbe-e7e5-48da-a7a7-cf7e27040d1b";
            
            //$id = 338;            
            //$token = "040b6a84-e217-4cfa-80e3-a9a9eb2c76ef";
            
            $id = 283;
            $token = "6Q7fdJQdhX_GyaE8h5qLD7fwQZ2QjwfE";
            
            $po = $rep->getPODetailsById($id, $token);
            $gr = GRDoc::createFromPo($po);
            
            var_dump($gr->getDocRows());
            
           
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
<?php
namespace ProcureTest\PO;

use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Service\FXService;
use Procure\Application\Service\PO\POService;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\PORowSnapshot;
use Procure\Domain\PurchaseOrder\POSnapshot;
use Procure\Domain\Service\POSpecService;
use PHPUnit_Framework_TestCase;

class POServiceTest extends PHPUnit_Framework_TestCase
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

            $data = array();
            $data["isActive"] = 1;
            $data["vendor"] = 229;
            $data["createdBy"] = 39;            
            $data["contractDate"] = "20191-08-08";
            $data["contractNo"] = "20191-08-08";
            $data["docCurrency"] = 100;
            $data["localCurrency"] = 2;
            $data["paymentTerm"] = 1;
            $data["company"] = 1;
            $data["exchangeRate"] = 1;
            
            $snapshot = SnapshotAssembler::createSnapShotFromArray($data, new POSnapshot());

            $rootEntity = PODoc::makeFromSnapshot($snapshot);
        
            $data["item"] = 2427;
            $data["docQuantity"] = 40;
            $data["docUnitPrice"] = 4089;
            $data["conversionFactor"] = 1;
            $rowSnapshot = SnapshotAssembler::createSnapShotFromArray($data, new PORowSnapshot());
            
            $sharedSpecificationFactory = new ZendSpecificationFactory($doctrineEM);
            $fxService = new FXService();
            $fxService->setDoctrineEM($doctrineEM);
            
            $specService = new POSpecService($sharedSpecificationFactory, $fxService);
            $rootEntity->addRowFromSnapshot($rowSnapshot, $specService);
            //$rootEntity->validate($specService);
            var_dump($rootEntity->getErrorMessage(false));
            
            
            /** @var POService $sv ; */
            $sv = Bootstrap::getServiceManager()->get('Procure\Application\Service\PO\POService');
            //$results = $sv->updateHeader(214,$dto,1,39);
           
            
            
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
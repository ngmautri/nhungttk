<?php
namespace ProcureTest\AP;


use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Procure\Domain\APInvoice\Factory\APFactory;
use Procure\Domain\APInvoice\APDocType;
use Procure\Domain\Service\APPostingService;
use Procure\Domain\Service\APSpecificationService;
use Procure\Application\Service\FXService;
use Procure\Infrastructure\Doctrine\DoctrineAPDocCmdRepository;
use Procure\Infrastructure\Doctrine\DoctrineAPDocQueryRepository;
use ProcureTest\Bootstrap;
use Procure\Domain\APInvoice\APDocSnapshotAssembler;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Doctrine\ORM\EntityManager;
use Procure\Domain\APInvoice\APDocRowSnapshotAssembler;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ApInvoiceVaidatorTest extends PHPUnit_Framework_TestCase
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

            $data["company"] = 1;
            $data["createdBy"] = 39;
            $data["vendor"] = 229;
            $data["invoiceDate"] = "2019-08-08";
            $data["postingDate"] = "2019-08-08";
            $data["grDate"] = "2019-08-08";
            
            
            $data["localCurrency"] = 2;
            $data["docCurrency"] = 248;
            $data["exchangeRate"] = 8690;
            
            $data["paymentTerm"] = 1;
            $data["warehouse"] = 5;
            $data["incoterm2"] = 15;
            $data["incotermPlace"] = "Vietnam";
            
            $snapshot = APDocSnapshotAssembler::createSnapshotFromArray($data);

            $rootEntity = APFactory::createAPDocument(APDocType::AP_INVOICE);
            $rootEntity->makeFromSnapshot($snapshot);
            
            
            
            $data["item"] = 2427;
            $data["docQuantity"] = 40;
            $data["glAccount"] = 12;
            $data["costCenter"] = 1;
            $data["conversionFactor"] = -1;
            
            $rowSnapshot = APDocRowSnapshotAssembler::createSnapshotFromArray($data);
            
            $sharedSpecificationFactory = new ZendSpecificationFactory($doctrineEM);
            $fxService = new FXService();
            $fxService->setDoctrineEM($doctrineEM);            
            $specificationService = new APSpecificationService($sharedSpecificationFactory, $fxService);            
            
            $rootEntity->addRowFromSnapshot($rowSnapshot, $specificationService);
        
            $apDocCmdRepository = new DoctrineAPDocCmdRepository($doctrineEM);
            $apDocQueryRepository = new DoctrineAPDocQueryRepository($doctrineEM);
            $postingService = new APPostingService($apDocCmdRepository, $apDocQueryRepository);

            var_dump($rootEntity->post($specificationService, $postingService));

            var_dump($rootEntity);
            // var_dump($item->createSnapshot());
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
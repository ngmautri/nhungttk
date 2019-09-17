<?php
namespace ProcureTest\PO;

use ProcureTest\Bootstrap;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\POSnapshot;
use PHPUnit_Framework_TestCase;
use Application\Domain\Shared\DTOFactory;
use Application\Domain\Shared\SnapshotAssembler;
use Procure\Application\Service\PO\POService;
use Money;
use Money\Currency;
use Money\Currencies\ISOCurrencies;
use Money\Parser\DecimalMoneyParser;
use Money\Formatter\IntlMoneyFormatter;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Converter;
use Money\Exchange\FixedExchange;
use Procure\Application\DTO\Po\PORowDetailsDTO;
use Procure\Domain\PurchaseOrder\PORowDetailsSnapshot;
use Procure\Domain\PurchaseOrder\PORow;
use Application\Application\Specification\Zend\ZendSpecificationFactory;
use Procure\Application\DTO\Po\PORowDTO;
use Procure\Domain\PurchaseOrder\PORowSnapshot;
use Procure\Domain\Service\POSpecificationService;
use Procure\Application\Service\FXService;

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
            $data["contractDate"] = "2019-08-08";
            $data["contractNo"] = "2019-08-08";
            $data["docCurrency"] = 1;
            $data["localCurrency"] = 2;
            $data["paymentTerm"] = 1;
            $data["company"] = 1;
            
            $snapshot = SnapshotAssembler::createSnapShotFromArray($data, new POSnapshot());

            $rootEntity = PODoc::makeFromSnapshot($snapshot);
            // var_dump($rootEntity);

            $data["item"] = 24271;
            $data["docQuantity"] = 40;
            $data["docUnitPrice"] = 40.89;
            $data["conversionFactor"] = 1;
            $rowSnapshot = SnapshotAssembler::createSnapShotFromArray($data, new PORowSnapshot());
            
            $sharedSpecificationFactory = new ZendSpecificationFactory($doctrineEM);
            $fxService = new FXService();
            $fxService->setDoctrineEM($doctrineEM);
            
            $specificationService = new POSpecificationService($sharedSpecificationFactory, $fxService);
            
            $rootEntity->addRowFromSnapshot($rowSnapshot, $specificationService);
            var_dump($rootEntity);
            
            /** @var POService $sv ; */
            $sv = Bootstrap::getServiceManager()->get('Procure\Application\Service\PO\POService');
            //$results = $sv->updateHeader(214,$dto,1,39);
           
            
            
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
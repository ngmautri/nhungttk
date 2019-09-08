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
            
            $data =  array();
            $data["isActive"] = 1;
            $data["vendor"] = 229;
            $data["contractDate"] = "2019-08-08";
            $data["contractNo"] = "2019-08-08";
            $data["docCurrency"] = 1;
            $data["localCurrency"] = 2;
            $data["paymentTerm"] = 1;
            
            $dto = DTOFactory::createDTOFromArray($data, new PoDTO());
            $snapshot = SnapshotAssembler::createSnapShotFromDTO($dto, new POSnapshot());
            $rootEntity = new PODoc();
            $rootEntity->makeFromSnapshot($snapshot);
            //var_dump($rootEntity);
            
            /** @var POService $sv ; */
            $sv = Bootstrap::getServiceManager()->get('Procure\Application\Service\PO\POService');
            $results = $sv->updateHeader(214,$dto,1,39);
            var_dump($results);
        } catch (InvalidArgumentException $e) {
            echo $e->getTrace();
        }
    }
}
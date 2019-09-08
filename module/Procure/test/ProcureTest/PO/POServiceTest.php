<?php
namespace ProcureTest\PO;

use ProcureTest\Bootstrap;
use Procure\Application\DTO\DTOFactory;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Domain\SnapshotAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\PODoc;
use Procure\Domain\PurchaseOrder\POSnapshot;
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
            
            $data =  array();
            $data["vendor"] = 229;
            $data["contractDate"] = "2019-012-08";
            $data["contractNo"] = "2019-08-08";
            $data["docCurrency"] = 1;
            $data["localCurrency"] = 10000;
            $data["paymentTerm"] = 1;
            
            $dto = DTOFactory::createDTOFromArray(new PoDTO(), $data);
            $snapshot = SnapshotAssembler::createSnapShotFromDTO($dto, new POSnapshot());
            $rootEntity = new PODoc();
            $rootEntity->makeFromSnapshot($snapshot);
            //var_dump($rootEntity);
            
            /** @var POService $sv ; */
            $sv = Bootstrap::getServiceManager()->get('Procure\Application\Service\PO\POService');
            $results = $sv->createHeader($dto,1,39);
            var_dump($results);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
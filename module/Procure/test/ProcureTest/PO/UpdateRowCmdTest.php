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
use Procure\Application\Command\PO\CreateHeaderCmd;
use Procure\Application\Command\PO\CreateHeaderCmdHandler;
use Application\Domain\Shared\DTOFactory;
use Procure\Application\DTO\Po\PoDTO;
use Application\Notification;
use Procure\Application\Command\PO\AddRowCmd;
use Procure\Application\Command\PO\AddRowCmdHandler;
use Procure\Application\DTO\Po\PORowDTO;

class AddRowCmdTest extends PHPUnit_Framework_TestCase
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

          
            $data["item"] = 4431;
            $data["isActive"] = 1;            
            $data["docQuantity"] = 40;
            $data["docUnitPrice"] = 4089;
            $data["conversionFactor"] = 1;

             $options = [
                "rootEntityId" => 302,
                "rootEntityToken" => "b69a9fbe-e7e5-48da-a7a7-cf7e27040d1b",
                "userId" => 39,
                "trigger" => null,
            ];
            
            $dto = DTOFactory::createDTOFromArray($data, new PORowDTO());
            
            $cmd = new AddRowCmd($doctrineEM, $dto, $options, new AddRowCmdHandler());
            $cmd->execute();
            
              
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
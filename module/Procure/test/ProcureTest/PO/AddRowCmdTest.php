<?php
namespace ProcureTest\PO;

use Application\Domain\Shared\DTOFactory;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\PO\AddRowCmd;
use Procure\Application\Command\PO\AddRowCmdHandler;
use Procure\Application\DTO\Po\PORowDTO;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

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
                "trigger" => null
            ];

            $dto = DTOFactory::createDTOFromArray($data, new PORowDTO());

            $cmd = new AddRowCmd($doctrineEM, $dto, $options, new AddRowCmdHandler());
            $cmd->execute();
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
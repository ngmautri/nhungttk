<?php
namespace InventoryTest\Item\Command;

use Doctrine\ORM\EntityManager;
use Inventory\Application\Command\GenericCmd;
use Inventory\Application\Command\Item\CreateCmdHandler;
use Inventory\Application\Command\Item\Options\CreateItemOptions;
use Inventory\Application\DTO\Item\ItemDTO;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use PHPUnit_Framework_TestCase;

class CreateCmdTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $companyId = 1;
            $userId = 39;

            $dto = new ItemDTO();

            $dto->itemTypeId = 1015;
            $dto->itemSku = "2-5-1";
            $dto->isActive = 1;
            $dto->itemName = "Special Item 2-5 updated5";
            $dto->itemDescription = "Special Item itemDescription";
            $dto->remarks = "Special Item itemDescription";
            $dto->standardUom = 1;
            $dto->stockUom = 1;
            $dto->stockUomConvertFactor = 1;
            $dto->createdBy = $userId;
            $dto->company = $companyId;
            $options = new CreateItemOptions($companyId, $userId, __METHOD__);

            $cmdHandler = new CreateCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($dto->getNotification()->getErrors());
        } catch (\Exception $e) {
            echo $e->getMessage();
            echo "====================";
            // echo $e->getTraceAsString();
        }
    }
}
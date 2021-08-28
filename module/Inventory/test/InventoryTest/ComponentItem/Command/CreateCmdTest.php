<?php
namespace InventoryTest\ComponentItem\Command;

use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Application\Command\GenericCmd;
use Inventory\Application\Command\TransactionalCmdHandlerDecorator;
use Inventory\Application\Command\Warehouse\CreateWarehouseCmdHandler;
use Inventory\Application\Command\Warehouse\Options\WhCreateOptions;
use Inventory\Application\DTO\Warehouse\WarehouseDTO;
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
            $localCurrencyId = 2;

            $dto = new WarehouseDTO();
            $dto->whName = 'wer';
            $dto->whCode = 'sdfdsf';
            $dto->whCountry = 2;

            $options = new WhCreateOptions($companyId, $localCurrencyId, $userId, __METHOD__);

            $cmdHandler = new CreateWarehouseCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($dto->getNotification());
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
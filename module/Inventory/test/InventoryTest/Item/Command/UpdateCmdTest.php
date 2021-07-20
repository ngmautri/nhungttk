<?php
namespace InventoryTest\Item\Command;

use Doctrine\ORM\EntityManager;
use Inventory\Application\Command\GenericCmd;
use Inventory\Application\Command\Item\UpdateCmdHandler;
use Inventory\Application\Command\Item\Options\UpdateItemOptions;
use Inventory\Application\DTO\Item\ItemDTO;
use Inventory\Application\EventBus\EventBusService;
use Inventory\Infrastructure\Doctrine\ItemQueryRepositoryImpl;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use PHPUnit_Framework_TestCase;

class UpdateCmdTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            $eventBusService = Bootstrap::getServiceManager()->get(EventBusService::class);

            $companyId = 1;
            $userId = 39;

            $rootEntityId = 5064;
            $rootEntityToken = "12485d89-26f8-40b9-b796-0c6db59f2901";
            $version = 13;

            $dto = new ItemDTO();
            $dto->isModel = "1";
            $dto->itemName = "Test AssetXYZ";
            $dto->itemSku = 'xx';

            $queryRep = new ItemQueryRepositoryImpl($doctrineEM);
            $rootEntity = $queryRep->getRootEntityByTokenId($rootEntityId, $rootEntityToken);
            $options = new UpdateItemOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);

            $cmdHandler = new UpdateCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator, $eventBusService);
            $cmd->execute();

            var_dump($dto->getNotification());
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n\n\n";
            echo $e->getTraceAsString();
        }
    }
}
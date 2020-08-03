<?php
namespace InventoryTest\Transaction\Command;

use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Application\Command\GenericCmd;
use Inventory\Application\Command\TransactionalCmdHandlerDecorator;
use Inventory\Application\Command\Transaction\PostCmdHandler;
use Inventory\Application\Command\Transaction\Options\TrxPostOptions;
use Inventory\Application\DTO\Transaction\TrxDTO;
use Inventory\Application\Eventbus\EventBusService;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class PostCmdTest extends PHPUnit_Framework_TestCase
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
            $logger = Bootstrap::getServiceManager()->get("AppLogger");

            $companyId = 1;
            $userId = 39;

            $rootEntityId = 1362;
            $rootEntityToken = "af6d3b5b-e838-479e-b989-3c15142ba37c";
            $version = 9;

            $rep = new TrxQueryRepositoryImpl($doctrineEM);
            $rootEntity = $rep->getRootEntityByTokenId($rootEntityId, $rootEntityToken);

            $options = new TrxPostOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);
            $dto = new TrxDTO();

            $cmdHandler = new PostCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator, $eventBusService);
            $cmd->setLogger($logger);
            $cmd->execute();
            var_dump($dto->getErrors());
            // var_dump($rootEntity);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
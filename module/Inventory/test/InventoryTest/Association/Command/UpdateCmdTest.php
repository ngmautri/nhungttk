<?php
namespace InventoryTest\Association\Command;

use Doctrine\ORM\EntityManager;
use Inventory\Application\Command\GenericCmd;
use Inventory\Application\Command\Association\UpdateCmdHandler;
use Inventory\Application\Command\Association\Options\UpdateOptions;
use Inventory\Application\DTO\Association\AssociationDTO;
use Inventory\Application\Eventbus\EventBusService;
use Inventory\Infrastructure\Doctrine\AssociationQueryRepositoryImpl;
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

            $rootEntityId = 3;
            $rootEntityToken = "7c10c765-0f50-451f-b9c5-bd292a9e622f";
            $version = 2;

            $dto = new AssociationDTO();
            $dto->association = 1;
            $dto->mainItem = 5099;
            $dto->relatedItem = 4054;
            $dto->isActive = 1;

            $queryRep = new AssociationQueryRepositoryImpl($doctrineEM);
            $rootEntity = $queryRep->getRootEntityByTokenId($rootEntityId, $rootEntityToken);
            $options = new UpdateOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);

            $cmdHandler = new UpdateCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator, $eventBusService);
            $cmd->execute();

            var_dump($dto->getNotification());
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n\n\n";
            // echo $e->getTraceAsString();
        }
    }
}
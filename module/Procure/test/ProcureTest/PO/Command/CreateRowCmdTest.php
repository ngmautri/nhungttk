<?php
namespace ProcureTest\PO\Command;

use Application\Application\Command\Doctrine\GenericCommand;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCommandHandler;
use Procure\Application\Command\Doctrine\PO\CreateRowCmdHandler;
use Procure\Application\Command\Options\CreateRowCmdOptions;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;
use Procure\Application\Eventbus\EventBusService;

class CreateRowCmdTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            $logger = Bootstrap::getServiceManager()->get('AppLogger');
            $eventBus = Bootstrap::getServiceManager()->get(EventBusService::class);
            $rootEntityId = 485;

            $rootEntityToken = "0fee41a0-d63e-4de8-a98c-e3be586dca58";

            $queryRep = new POQueryRepositoryImpl($doctrineEM);
            $rootEntity = $queryRep->getHeaderById($rootEntityId, $rootEntityToken);
            $version = 16;

            $data["item"] = 4431;
            $data["isActive"] = 1;
            $data["docQuantity"] = 40;
            $data["docUnitPrice"] = 4089;
            $data["conversionFactor"] = 1;

            $userId = 39;
            $options = new CreateRowCmdOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);
            $cmdHandler = new CreateRowCmdHandler();
            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);

            $cmd = new GenericCommand($doctrineEM, $data, $options, $cmdHandlerDecorator, $eventBus);
            $cmd->setLogger($logger);
            $cmd->execute();
            // \var_dump($cmd->getNotification());
            \var_dump($cmd->getOutput());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
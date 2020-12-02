<?php
namespace ProcureTest\GR\Command;

use Application\Application\Command\Doctrine\GenericCommand;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TestTransactionalCommandHandler;
use Procure\Application\Command\Doctrine\AP\PostCmdHandler;
use Procure\Application\Command\Options\PostCmdOptions;
use Procure\Application\Eventbus\EventBusService;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class PostCmdTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testCanNotPost()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            $eventBusService = Bootstrap::getServiceManager()->get(EventBusService::class);

            $userId = 39;

            $rootEntityId = 3579;
            $rootEntityToken = "2b46203c-2928-4b01-a7a9-fadc1408a2c7";
            $version = null;

            $rep = new APQueryRepositoryImpl($doctrineEM);
            $rootEntity = $rep->getRootEntityByTokenId($rootEntityId, $rootEntityToken);

            $options = new PostCmdOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);
            $cmdHandler = new PostCmdHandler();
            $cmdHandlerDecorator = new TestTransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($doctrineEM, null, $options, $cmdHandlerDecorator, $eventBusService);
            $cmd->execute();
        } catch (\Exception $e) {
            $this->assertTrue($cmd->getNotification()
                ->hasErrors());
            \var_dump($cmd->getNotification());
        }
    }
}
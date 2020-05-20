<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\GenericCmd;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use Procure\Application\Command\AP\PostCmdHandler;
use Procure\Application\Command\AP\Options\ApPostOptions;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Application\Eventbus\EventBusService;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
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

            $companyId = 1;
            $userId = 39;

            // $rootEntityId = 2875;
            // $rootEntityToken = "3b46358b-64ee-469c-bb59-42a3fe6fc1e5";
            // $version = 9;

            $rootEntityId = 2825;
            $rootEntityToken = "ec82eb88-f1ad-4dfa-b4bd-cff6f76a1816";
            $version = 12;

            $rep = new APQueryRepositoryImpl($doctrineEM);
            $rootEntity = $rep->getRootEntityByTokenId($rootEntityId, $rootEntityToken);

            $options = new ApPostOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);
            $dto = new ApDTO();

            $cmdHandler = new PostCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator, $eventBusService);
            $cmd->execute();
            var_dump($dto->getErrors());
            // var_dump($rootEntity);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
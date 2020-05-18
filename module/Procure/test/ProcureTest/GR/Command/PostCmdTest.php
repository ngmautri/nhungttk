<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\GenericCmd;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use Procure\Application\Command\GR\PostCmdHandler;
use Procure\Application\Command\GR\Options\GrPostOptions;
use Procure\Application\DTO\Gr\GrDTO;
use Procure\Application\Eventbus\EventBusService;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class SaveFromPOCmdTest extends PHPUnit_Framework_TestCase
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

            $rootEntityId = "91";
            $rootEntityToken = "c7cd7d4b-b3a5-4a36-b894-7de498a5f0f8";
            $version = 19;

            $rep = new GRQueryRepositoryImpl($doctrineEM);
            $rootEntity = $rep->getRootEntityByTokenId($rootEntityId, $rootEntityToken);

            $options = new GrPostOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);

            $dto = new GrDTO();

            $cmdHandler = new PostCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator, $eventBusService);
            $cmd->execute();
            var_dump($rootEntity->getRecordedEvents());
            var_dump($dto->getErrors());

            // var_dump($rootEntity);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
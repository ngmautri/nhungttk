<?php
namespace ProcureTest\AP\Command;

use Doctrine\ORM\EntityManager;
use Inventory\Application\Command\GenericCmd;
use Inventory\Application\Command\Transaction\UpdateHeaderCmdHandler;
use Inventory\Application\Command\Transaction\Options\TrxUpdateOptions;
use Inventory\Application\DTO\Transaction\TrxDTO;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use PHPUnit_Framework_TestCase;

class UpdateHeaderCmdTest extends PHPUnit_Framework_TestCase
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

            $rootEntityId = 1356;
            $rootEntityToken = "093c99ba-a3ad-40b9-a6fd-f1288a0131a5";
            $version = 2;

            $dto = new TrxDTO();
            $dto->movementDate = "2020-04-02";
            $dto->isActive = 1;
            $dto->warehouse = 5;

            $queryRep = new TrxQueryRepositoryImpl($doctrineEM);
            $rootEntity = $queryRep->getHeaderById($rootEntityId, $rootEntityToken);
            $options = new TrxUpdateOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__, false);

            $cmdHandler = new UpdateHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();

            var_dump($dto->getNotification());
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n\n\n";
            // echo $e->getTraceAsString();
        }
    }
}
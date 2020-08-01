<?php
namespace InventoryTest\Transaction\Command;

use Doctrine\ORM\EntityManager;
use Inventory\Application\Command\GenericCmd;
use Inventory\Application\Command\Transaction\CreateRowCmdHandler;
use Inventory\Application\Command\Transaction\Options\TrxRowCreateOptions;
use Inventory\Application\DTO\Transaction\TrxRowDTO;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use PHPUnit_Framework_TestCase;

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

            $rootEntityId = 1356;
            $rootEntityToken = "093c99ba-a3ad-40b9-a6fd-f1288a0131a5";
            $version = 1;

            $rep = new TrxQueryRepositoryImpl($doctrineEM);
            $rootEntity = $rep->getHeaderById($rootEntityId, $rootEntityToken);

            $companyId = 1;
            $userId = 39;

            $dto = new TrxRowDTO();
            $dto->docQuantity = 12;
            $dto->docUnitPrice = 12;
            $dto->item = 12427;
            $dto->conversionFactor = 1;
            $dto->unit = "pcs";
            $dto->flow = $rootEntity->getMovementFlow();
            $dto->glAccount = 6;
            $dto->costCenter = - 2;
            $dto->wh = 5;

            $options = new TrxRowCreateOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);

            $cmdHandler = new CreateRowCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($dto->getNotification());
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
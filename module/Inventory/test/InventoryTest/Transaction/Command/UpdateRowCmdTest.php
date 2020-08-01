<?php
namespace InventoryTest\Transation\Command;

use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Application\Command\GenericCmd;
use Inventory\Application\Command\TransactionalCmdHandlerDecorator;
use Inventory\Application\Command\Transaction\UpdateRowCmdHandler;
use Inventory\Application\Command\Transaction\Options\TrxRowUpdateOptions;
use Inventory\Application\DTO\Transaction\TrxRowDTO;
use Inventory\Infrastructure\Doctrine\TrxQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class UpdateRowCmdTest extends PHPUnit_Framework_TestCase
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

            $dto = new TrxRowDTO();
            $dto->isActive = 1;
            $dto->docQuantity = 15;
            $dto->docUnitPrice = 20.5;

            $rootEntityId = 1356;
            $rootEntityToken = "093c99ba-a3ad-40b9-a6fd-f1288a0131a5";
            $version = 7;

            $entityId = 7105;
            $entityToken = "3c7a064d-c04f-4f89-a144-f185a776d431";

            $rep = new TrxQueryRepositoryImpl($doctrineEM);
            $rootEntity = $rep->getRootEntityByTokenId($rootEntityId, $rootEntityToken);
            // var_dump($rootEntity->getDocRows());

            $localEntity = $rootEntity->getRowbyId($entityId);

            $options = new TrxRowUpdateOptions($rootEntity, $localEntity, $entityId, $entityToken, $version, $userId, __METHOD__);

            $cmdHandler = new UpdateRowCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($dto->getNotification());
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
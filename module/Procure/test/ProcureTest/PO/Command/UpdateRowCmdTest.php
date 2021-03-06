<?php
namespace ProcureTest\PO\Command;

use Application\Application\Command\Doctrine\GenericCommand;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCommandHandler;
use Procure\Application\Command\Doctrine\PO\UpdateRowCmdHandler;
use Procure\Application\Command\Options\UpdateRowCmdOptions;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
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

            $rootEntityId = 485;
            $rootEntityToken = "0fee41a0-d63e-4de8-a98c-e3be586dca58";
            $version = 32;
            $queryRep = new POQueryRepositoryImpl($doctrineEM);
            $rootEntity = $queryRep->getPODetailsById($rootEntityId, $rootEntityToken);

            $entityId = 3630;
            $entityToken = '';
            $localEntity = $rootEntity->getRowbyId($entityId);

            $data["item"] = 4431;
            $data["docUnit"] = 'box';
            $data["isActive"] = 1;
            $data["docQuantity"] = '100,9';
            $data["docUnitPrice"] = '7500,500';
            $data["standardConvertFactor"] = 25;

            $userId = 39;
            $options = new UpdateRowCmdOptions($rootEntity, $localEntity, $entityId, $entityToken, $version, $userId, __METHOD__);
            $options->setLocale('vn_DE');

            $cmdHandler = new UpdateRowCmdHandler();
            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);

            $cmd = new GenericCommand($doctrineEM, $data, $options, $cmdHandlerDecorator);
            $cmd->execute();
             \var_dump($cmd->getNotification());
            // \var_dump($cmd->getOutput());
             //\var_dump($cmd->getEstimatedDuration());
        } catch (\Exception $e) {
            //\var_dump($e->getTraceAsString());
            \var_dump($e->getMessage());
        }
    }
}
<?php
namespace ProcureTest\PO\Command;

use Application\Application\Command\Doctrine\GenericCommand;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCommandHandler;
use Procure\Application\Command\Doctrine\PO\UpdateHeaderCmdHandler;
use Procure\Application\Command\Options\UpdateHeaderCmdOptions;
use Procure\Infrastructure\Doctrine\POQueryRepositoryImpl;
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

            $rootEntityId = 485;
            $rootEntityToken = "0fee41a0-d63e-4de8-a98c-e3be586dca58";
            $version = 6;

            $queryRep = new POQueryRepositoryImpl($doctrineEM);

            $rootEntity = $queryRep->getHeaderById($rootEntityId, $rootEntityToken);

            $data = array();
            $data["isActive"] = 1;
            $data["vendor"] = 229;
            $data["createdBy"] = 39;
            $data["docDate"] = "2019-08-08";
            $data["docNumber"] = "2019-08-08";
            $data["docCurrency"] = 100;
            $data["localCurrency"] = 2;
            $data["pmtTerm"] = 1;
            $data["company"] = 1;
            $data["exchangeRate"] = 1;

            $userId = 39;
            $options = new UpdateHeaderCmdOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);
            $cmdHandler = new UpdateHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);

            $cmd = new GenericCommand($doctrineEM, $data, $options, $cmdHandlerDecorator);
            $cmd->execute();
            // \var_dump($cmd->getNotification());
            \var_dump($cmd->getOutput());
        } catch (\Exception $e) {
            // \var_dump($e->getTraceAsString());
            \var_dump($e->getMessage());
        }
    }
}
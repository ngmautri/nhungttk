<?php
namespace ApplicationTest\AccountChart\Command;

use ApplicationTest\Bootstrap;
use Application\Application\Command\TransactionalCommandHandler;
use Application\Application\Command\Doctrine\GenericCommand;
use Application\Application\Command\Doctrine\Company\Warehouse\RemoveLocationCmdHandler;
use Application\Application\Command\Options\UpdateMemberCmdOptions;
use Inventory\Domain\Warehouse\GenericWarehouse;
use Inventory\Infrastructure\Doctrine\WhQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class RemoveLocationCmdTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new \Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl($doctrineEM);
            $company = $rep->getById(1);
            $companyVO = $company->createValueObject();

            $rep1 = new WhQueryRepositoryImpl($doctrineEM);

            /**
             *
             * @var GenericWarehouse $wh ;
             */
            $wh = $rep1->getById(5);
            $location = $wh->getLocationByCode('BIN 8');

            $userId = 39;
            $data = null;
            $rootEntity = $wh;
            $localEntity = $location;
            $entityId = 5;
            $entityToken = null;
            $version = null;
            $userId = 39;

            $options = new UpdateMemberCmdOptions($companyVO, $rootEntity, $localEntity, $entityId, $entityToken, $version, $userId, __METHOD__);

            $cmdHandler = new RemoveLocationCmdHandler();
            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($doctrineEM, $data, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($cmd->getNotification()->successMessage());
        } catch (\Exception $e) {
            echo $e->getMessage();
            echo "====================";
            echo $e->getTraceAsString();
        }
    }
}
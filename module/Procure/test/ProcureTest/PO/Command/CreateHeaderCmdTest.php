<?php
namespace ProcureTest\PO\Command;

use Application\Application\Command\Doctrine\GenericCommand;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCommandHandler;
use Procure\Application\Command\Doctrine\PO\CreateHeaderCmdHandler;
use Procure\Application\Command\Options\CreateHeaderCmdOptions;
use PHPUnit_Framework_TestCase;

class CreateHeaderCmdTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

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
            $companyId = 1;
            $options = new CreateHeaderCmdOptions($companyId, $userId, __METHOD__);

            $cmdHandler = new CreateHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);

            $cmd = new GenericCommand($doctrineEM, $data, $options, $cmdHandlerDecorator);
            $cmd->execute();
            \var_dump($cmd->getNotification());
        } catch (\Exception $e) {
            // \var_dump($e->getTraceAsString());
            \var_dump($e->getMessage());
        }
    }
}
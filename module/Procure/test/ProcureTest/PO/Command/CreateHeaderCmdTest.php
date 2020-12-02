<?php
namespace ProcureTest\PO\Command;

use Application\Application\Command\Doctrine\GenericCommand;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TestTransactionalCommandHandler;
use Procure\Application\Command\Doctrine\PO\CreateHeaderCmdHandler;
use Procure\Application\Command\Options\CreateHeaderCmdOptions;
use Procure\Application\DTO\Po\PoDTO;
use PHPUnit_Framework_TestCase;

class CreateHeaderCmdTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testCanCreatePO()
    {
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
        $cmdHandlerDecorator = new TestTransactionalCommandHandler($cmdHandler);

        $cmd = new GenericCommand($doctrineEM, $data, $options, $cmdHandlerDecorator);
        $cmd->execute();

        $header = $cmd->getOutput();
        $this->assertInstanceOf(PoDTO::class, $header);
        $this->assertTrue($header->getId() > 0);
    }

    public function testCanNotCreatePO()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $data = array();
            $data["isActive"] = 1;
            $data["vendor"] = 12000;
            $data["docDate"] = "12019-08-08";
            $data["docNumber"] = "12019-08-08";
            $data["docCurrency"] = 248;
            $data["localCurrency"] = 2;
            $data["pmtTerm"] = 0;
            $data["company"] = 15;
            $data["exchangeRate"] = 1;

            $userId = 390;
            $companyId = 1;
            $options = new CreateHeaderCmdOptions($companyId, $userId, __METHOD__);

            $cmdHandler = new CreateHeaderCmdHandler();
            $cmdHandlerDecorator = new TestTransactionalCommandHandler($cmdHandler);

            $cmd = new GenericCommand($doctrineEM, $data, $options, $cmdHandlerDecorator);

            $cmd->execute();
            $this->assertTrue($cmd->getNotification()
                ->hasErrors());
        } catch (\Exception $e) {
            \var_dump($cmd->getNotification());
        }
    }
}
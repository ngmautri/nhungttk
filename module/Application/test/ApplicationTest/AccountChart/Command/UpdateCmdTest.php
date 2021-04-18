<?php
namespace InventoryTest\Item\Command;

use Application\Application\Command\TransactionalCommandHandler;
use Application\Application\Command\Doctrine\GenericCommand;
use Application\Application\Command\Doctrine\Company\AccountChart\CreateChartCmdHandler;
use Application\Application\Command\Options\CmdOptions;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
use ProcureTest\Bootstrap;
use PHPUnit_Framework_TestCase;

class UpdateCmdTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            $rep = new CompanyQueryRepositoryImpl($doctrineEM);
            // $filter = new CompanyQuerySqlFilter();
            $company = $rep->getById(1);

            $userId = 39;

            $data = [
                'coaCode' => 'LAS_2009',
                'coaName' => 'Chart of Laos 2009'
            ];

            $options = new CmdOptions($company->createValueObject(), $userId, __METHOD__);

            $cmdHandler = new CreateChartCmdHandler();
            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($doctrineEM, $data, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($cmd->getNotification());
        } catch (\Exception $e) {
            echo $e->getMessage();
            echo "====================";
            // echo $e->getTraceAsString();
        }
    }
}
}
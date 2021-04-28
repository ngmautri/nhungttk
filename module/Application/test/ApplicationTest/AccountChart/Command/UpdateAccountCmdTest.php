<?php
namespace ApplicationTest\AccountChart\Command;

use ApplicationTest\Bootstrap;
use Application\Application\Command\TransactionalCommandHandler;
use Application\Application\Command\Doctrine\GenericCommand;
use Application\Application\Command\Doctrine\Company\AccountChart\UpdateAccountCmdHandler;
use Application\Application\Command\Options\UpdateMemberCmdOptions;
use Application\Infrastructure\Persistence\Domain\Doctrine\ChartQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class UpdateAccountCmdTest extends PHPUnit_Framework_TestCase
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
            $rep = new ChartQueryRepositoryImpl($doctrineEM);
            // $filter = new CompanyQuerySqlFilter();
            $chart = $rep->getById(13);
            $account = $chart->getAccountById(17);

            $userId = 39;
            $data = [
                'accountName' => '201',
                'accountNumber' => '101',
                'remarks' => 'LAS_2009 updated'
            ];

            $rootEntity = $chart;
            $localEntity = $account;
            $entityId = 13;
            $entityToken = null;
            $version = null;
            $userId = 39;

            $options = new UpdateMemberCmdOptions($companyVO, $rootEntity, $localEntity, $entityId, $entityToken, $version, $userId, __METHOD__);

            $cmdHandler = new UpdateAccountCmdHandler();
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
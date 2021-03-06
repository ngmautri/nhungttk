<?php
namespace ProcureTest\AP\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use Procure\Application\Command\AP\EditHeaderCmd;
use Procure\Application\Command\AP\EditHeaderCmdHandler;
use Procure\Application\Command\AP\Options\ApUpdateOptions;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class UpdateHeaderCmdTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(dirname(__FILE__)))));
        // echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $companyId = 1;
            $userId = 39;

            $rootEntityId = 2828;
            $rootEntityToken = "3c1b51e9-f6f9-4298-946f-d58b49428571";
            $version = 7;

            $dto = new ApDTO();
            $dto->docCurrency = 248;
            $dto->vendor = 229;
            $dto->warehouse = 5;
            $dto->grDate = "2020-04-06";
            $dto->postingDate = "2020-04-06";
            $dto->docDate = "2020-04-06";
            $dto->pmtTerm = 1;
            $dto->docNumber = "SDD7";

            $queryRep = new APQueryRepositoryImpl($doctrineEM);

            $rootEntity = $queryRep->getHeaderById($rootEntityId, $rootEntityToken);
            $options = new ApUpdateOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__, false);

            $cmdHandler = new EditHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new EditHeaderCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();

            var_dump($dto->getNotification());
        } catch (\Exception $e) {
            echo $e->getMessage() . "\n\n\n";
            // echo $e->getTraceAsString();
        }
    }
}
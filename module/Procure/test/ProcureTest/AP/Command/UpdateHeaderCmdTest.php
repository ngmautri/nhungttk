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
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $companyId = 1;
            $userId = 39;

            $rootEntityId = 2825;
            $rootEntityToken = "ec82eb88-f1ad-4dfa-b4bd-cff6f76a1816";
            $version = 7;

            $dto = new ApDTO();
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
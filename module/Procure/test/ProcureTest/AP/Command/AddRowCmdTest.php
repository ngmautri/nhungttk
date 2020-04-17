<?php
namespace ProcureTest\AP\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use Procure\Application\Command\AP\AddRowCmd;
use Procure\Application\Command\AP\AddRowCmdHandler;
use Procure\Application\Command\AP\Options\ApRowCreateOptions;
use Procure\Application\DTO\Ap\ApRowDTO;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class AddRowCmdTest extends PHPUnit_Framework_TestCase
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

            $dto = new ApRowDTO();
            $dto->docQuantity = 10;
            $dto->docUnitPrice = 229;
            $dto->poRow = 2629;
            $dto->item = 4245;
            $dto->conversionFactor = 1;
            $dto->unit = "pcs";
            $dto->glAccount = 6;
            $dto->costCenter = 2;

            $rootEntityId = 2828;
            $rootEntityToken = "3c1b51e9-f6f9-4298-946f-d58b49428571";
            $version = 9;

            $rep = new APQueryRepositoryImpl($doctrineEM);
            $rootEntity = $rep->getHeaderById($rootEntityId, $rootEntityToken);

            $options = new ApRowCreateOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);

            $cmdHandler = new AddRowCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new AddRowCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($dto->getNotification());
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
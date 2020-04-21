<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use Procure\Application\Command\AP\UpdateRowCmd;
use Procure\Application\Command\AP\UpdateRowCmdHandler;
use Procure\Application\Command\AP\Options\ApRowUpdateOptions;
use Procure\Application\DTO\Ap\ApRowDTO;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class UpdateRowCmdTest extends PHPUnit_Framework_TestCase
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
            $dto->docUnitPrice = 20.5;
            $dto->poRow = 2629;
            $dto->item = 4245;
            $dto->conversionFactor = 1;
            $dto->unit = "pcs";

            $rootEntityId = 2828;
            $rootEntityToken = "3c1b51e9-f6f9-4298-946f-d58b49428571";
            $version = 11;

            $entityId = 8940;
            $entityToken = "4eb5fba5-2cae-43b0-bf29-037482bfd458";

            $rep = new APQueryRepositoryImpl($doctrineEM);
            $rootEntity = $rep->getRootEntityByTokenId($rootEntityId, $rootEntityToken);
            // var_dump($rootEntity->getDocRows());

            $localEntity = $rootEntity->getRowbyId($entityId);

            $options = new ApRowUpdateOptions($rootEntity, $localEntity, $entityId, $entityToken, $version, $userId, __METHOD__);

            $cmdHandler = new UpdateRowCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new UpdateRowCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($dto->getNotification());
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
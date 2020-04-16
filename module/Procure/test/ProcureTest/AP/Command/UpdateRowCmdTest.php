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
            $dto->docUnitPrice = 22;
            $dto->poRow = 2629;
            $dto->item = 4245;
            $dto->conversionFactor = 1;
            $dto->unit = "pcs";

            $rootEntityId = 28199;
            $rootEntityToken = "8178443a-55ef-44e8-a819-874e68480614";
            $version = 11;

            $entityId = 8933;
            $entityToken = "e5503296-f3c6-4386-a08b-56526e640674";

            $rep = new APQueryRepositoryImpl($doctrineEM);
            $rootEntity = $rep->getRootEntityByTokenId($rootEntityId, $rootEntityToken);
            // var_dump($rootEntity->getDocRows());

            $localEntity = $rootEntity->getRowbyId($entityId);

            $options = new ApRowUpdateOptions($rootEntity, $localEntity, $entityId, $entityToken, $version, $userId, __METHOD__);

            $cmdHandler = new UpdateRowCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new UpdateRowCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($dto->getErrors());
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
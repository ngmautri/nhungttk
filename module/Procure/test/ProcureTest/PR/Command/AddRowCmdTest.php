<?php
namespace ProcureTest\PR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use Procure\Application\Command\GR\AddRowCmdHandler;
use Procure\Application\Command\GR\Options\GrRowCreateOptions;
use Procure\Application\DTO\Gr\GrRowDTO;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class AddRowCmdTest extends PHPUnit_Framework_TestCase
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

            $dto = new GrRowDTO();
            $dto->docQuantity = 248;
            $dto->docUnitPrice = 229;
            $dto->poRow = 2629;
            $dto->item = 4245;
            $dto->conversionFactor = 1;
            $dto->unit = "pcs";

            $rootEntityId = "94";
            $rootEntityToken = "cc15908b-e12f-4403-bbda-ceb5d824f1f5";
            $version = 0;

            $rep = new GRQueryRepositoryImpl($doctrineEM);
            $rootEntity = $rep->getHeaderById($rootEntityId, $rootEntityToken);

            $options = new GrRowCreateOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);
            $cmdHandler = new AddRowCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new AddRowCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($dto->getErrors());
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
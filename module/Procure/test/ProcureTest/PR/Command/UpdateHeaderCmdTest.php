<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\GR\EditHeaderCmd;
use Procure\Application\Command\GR\EditHeaderCmdHandler;
use Procure\Application\Command\GR\Options\GrUpdateOptions;
use Procure\Application\DTO\Gr\GrDTO;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;
use Procure\Application\Command\TransactionalCmdHandlerDecoratorTest;

class SaveFromPOCmdTest extends PHPUnit_Framework_TestCase
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

            $rootEntityId = 94;
            $rootEntityToken = "cc15908b-e12f-4403-bbda-ceb5d824f1f5";
            $version = 0;

            $dto = new GrDTO();
            $dto->docCurrency = 2481;
            $dto->vendor = 229;
            $dto->warehouse = 5;
            $dto->grDate = "2020-03-06";

            $queryRep = new GRQueryRepositoryImpl($doctrineEM);            
            
            $rootEntity = $queryRep->getHeaderById($rootEntityId,$rootEntityToken);           
            $options = new GrUpdateOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);
            
            $cmdHandler = new EditHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecoratorTest($cmdHandler);
            $cmd= new EditHeaderCmd($doctrineEM, $dto, $options,$cmdHandlerDecorator);
            $cmd->execute();           

            var_dump($dto->getErrors());            
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
        }
    }
}
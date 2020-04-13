<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCmdHandlerDecoratorTest;
use Procure\Application\Command\GR\PostCmd;
use Procure\Application\Command\GR\PostCmdHandler;
use Procure\Application\Command\GR\Options\GrPostOptions;
use Procure\Application\DTO\Gr\GrDTO;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

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

        
            $rootEntityId = "94";
            $rootEntityToken = "cc15908b-e12f-4403-bbda-ceb5d824f1f5";
            $version = 3;

            $rep = new GRQueryRepositoryImpl($doctrineEM);
            $rootEntity = $rep->getRootEntityByTokenId($rootEntityId, $rootEntityToken);

            $options = new GrPostOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);

            $dto = new GrDTO();
            
            $cmdHandler = new PostCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecoratorTest($cmdHandler);
            $cmd = new PostCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            
            var_dump($dto->getErrors());
            
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
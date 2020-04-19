<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCmdHandlerDecoratorTest;
use Procure\Application\Command\AP\ReverseCmdHandler;
use Procure\Application\Command\AP\Options\ApReverseOptions;
use Procure\Application\Command\Ap\ReverseCmd;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class PostCmdTest extends PHPUnit_Framework_TestCase
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

            $rootEntityId = 2832;
            $rootEntityToken = "bd947bde-8103-4a1f-88c6-ba01c75582cb";
            $version = 4;

            $rep = new APQueryRepositoryImpl($doctrineEM);
            $rootEntity = $rep->getRootEntityByTokenId($rootEntityId, $rootEntityToken);

            $options = new ApReverseOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);

            $dto = new ApDTO();

            $cmdHandler = new ReverseCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecoratorTest($cmdHandler);
            $cmd = new ReverseCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($dto->getNotification());
            var_dump($rootEntity);
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
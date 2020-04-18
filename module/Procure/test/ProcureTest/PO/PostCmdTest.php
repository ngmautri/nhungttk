<?php
namespace ProcureTest\PO;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\PO\PostCmd;
use Procure\Application\Command\PO\PostCmdHandler;
use Procure\Application\Command\PO\PostCmdHandlerDecorator;
use Procure\Application\Command\PO\Options\PoPostOptions;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use Procure\Application\Command\TransactionalCmdHandlerDecoratorTest;

class PostCmdTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        // echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            $sv = Bootstrap::getServiceManager()->get('Procure\Application\Service\PO\POService');

            $entity_id = 351;
            $entity_token = "b97ba14a-9587-4efa-980c-dafe747acd20";
            $version = 1;
            $userId = 39;
            $dto = new PoDTO();

            $rootEntity = $sv->getPODetailsById($entity_id, $entity_token);

            $options = new PoPostOptions($rootEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);
            $cmdHandler = new PostCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecoratorTest($cmdHandler);

            $cmd = new PostCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($dto);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
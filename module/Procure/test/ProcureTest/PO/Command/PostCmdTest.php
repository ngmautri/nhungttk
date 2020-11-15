<?php
namespace ProcureTest\PO\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCmdHandlerDecoratorTest;
use Procure\Application\Command\Options\PostCmdOptions;
use Procure\Application\Command\PO\PostCmdHandler;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Application\Service\PO\POService;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

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

            /** @var POService $sv ; */
            $sv = Bootstrap::getServiceManager()->get('Procure\Application\Service\PO\POService');

            $rootEntityId = 485;
            $rootEntityToken = "0fee41a0-d63e-4de8-a98c-e3be586dca58";
            $version = 6;

            $userId = 39;
            $dto = new PoDTO();

            $rootEntity = $sv->get($rootEntityId, $rootEntityToken);

            $options = new PostCmdOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);
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
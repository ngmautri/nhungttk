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
            $sv= Bootstrap::getServiceManager()->get('Procure\Application\Service\PO\POService');
            
            $entity_id = 346;
            $entity_token = "5279c722-e2ac-43e5-9f49-23cf3f8fcd27";
            $version = 1;
            $userId =39;
            $dto = new PoDTO();
            
            $rootEntity = $sv->getPODetailsById($entity_id, $entity_token);
            
            
            $options = new PoPostOptions($rootEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);
            $cmdHandler = new PostCmdHandler();
            $cmdHandlerDecorator = new PostCmdHandlerDecorator($cmdHandler);
            
            $cmd = new PostCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($dto);
            
              
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
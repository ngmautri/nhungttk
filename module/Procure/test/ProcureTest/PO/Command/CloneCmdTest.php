<?php
namespace ProcureTest\PO\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCmdHandlerDecoratorTest;
use Procure\Application\Command\PO\CloneAndSavePOCmdHandler;
use Procure\Application\Command\PO\PostCmdHandler;
use Procure\Application\Command\PO\Options\PoUpdateOptions;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Procure\Application\Command\GenericCmd;

class CloneCmdTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            $sv = Bootstrap::getServiceManager()->get('Procure\Application\Service\PO\POService');
            $logger = Bootstrap::getServiceManager()->get('ProcureLogger');

            $entity_id = 465;
            $entity_token = "9a124baf-da3e-4ae6-91a3-7d8090bca397";
            $version = 1;
            $userId = 39;
            $rootEntity = $sv->getPODetailsById($entity_id, $entity_token);
            $dto = new PoDTO();

            $options = new PoUpdateOptions($rootEntity, $entity_id, $entity_token, $version, $userId, __METHOD__);
            $cmdHandler = new CloneAndSavePOCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecoratorTest($cmdHandler);

            $cmd = new GenericCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->setLogger($logger);
            $cmd->execute();
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }
}
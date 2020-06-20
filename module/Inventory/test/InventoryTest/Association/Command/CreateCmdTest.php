<?php
namespace InventoryTest\Association\Command;

use Doctrine\ORM\EntityManager;
use Inventory\Application\Command\GenericCmd;
use Inventory\Application\Command\Association\CreateCmdHandler;
use Inventory\Application\Command\Association\Options\CreateOptions;
use Inventory\Application\DTO\Association\AssociationDTO;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use PHPUnit_Framework_TestCase;

class CreateCmdTest extends PHPUnit_Framework_TestCase
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

            $dto = new AssociationDTO();
            $dto->association = 2;
            $dto->mainItem = 3355;
            $dto->relatedItem = 3355;

            $dto->createdBy = $userId;
            $dto->company = $companyId;

            $options = new CreateOptions($companyId, $userId, __METHOD__);

            $cmdHandler = new CreateCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new GenericCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($dto->getNotification()->getErrors());
        } catch (\Exception $e) {
            echo $e->getMessage();
            echo "\n==================== ";
            // echo $e->getTraceAsString();
        }
    }
}
<?php
namespace ProcureTest\QR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\GenericCmd;
use Procure\Application\Command\TransactionalCmdHandlerDecoratorTest;
use Procure\Application\Command\QR\CreateHeaderCmdHandler;
use Procure\Application\Command\QR\Options\CreateOptions;
use Procure\Application\DTO\Qr\QrDTO;
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

            $dto = new QrDTO();
            $dto->docNumber = "SDD";
            $dto->docDate = "2020-04-02";
            $dto->docCurrency = 248;
            $dto->vendor = 229;
            $dto->warehouse = 5;
            $dto->pmtTerm = 1;

            $options = new CreateOptions($companyId, $userId, __METHOD__);

            $cmdHandler = new CreateHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecoratorTest($cmdHandler);
            $cmd = new GenericCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($dto->getNotification());
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
        }
    }
}
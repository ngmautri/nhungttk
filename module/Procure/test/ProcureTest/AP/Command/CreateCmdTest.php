<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use Procure\Application\Command\AP\CreateHeaderCmd;
use Procure\Application\Command\AP\CreateHeaderCmdHandler;
use Procure\Application\Command\AP\Options\ApCreateOptions;
use Procure\Application\DTO\Ap\ApDTO;
use PHPUnit_Framework_TestCase;

class CreateCmdTest extends PHPUnit_Framework_TestCase
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

            $dto = new ApDTO();
            $dto->docNumber = "SDD";
            $dto->docDate = "2020-04-02";
            $dto->docCurrency = 248;
            $dto->vendor = 229;
            $dto->warehouse = 5;
            $dto->grDate = "2020-04-02";
            $dto->postingDate = "2020-04-02";
            $dto->pmtTerm = 1;

            $options = new ApCreateOptions($companyId, $userId, __METHOD__);

            $cmdHandler = new CreateHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new CreateHeaderCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($dto->getNotification());
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
        }
    }
}
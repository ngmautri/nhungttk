<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCmdHandlerDecoratorTest;
use Procure\Application\Command\AP\SaveCopyFromPOCmd;
use Procure\Application\Command\AP\SaveCopyFromPOCmdHandler;
use Procure\Application\Command\AP\Options\CopyFromPOOptions;
use Procure\Application\Command\AP\Options\SaveCopyFromPOOptions;
use Procure\Application\DTO\Ap\ApDTO;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Application\Service\AP\APService;
use Procure\Domain\AccountPayable\APDoc;
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

            /**
             *
             * @var APService $sv ;
             */
            $sv = Bootstrap::getServiceManager()->get('Procure\Application\Service\AP\APService');

            $source_id = 348;
            $source_token = "c19339ef-ee7a-4565-8e5f-7fecda95574d";
            $version = 1;
            $userId = 39;
            $dto = new PoDTO();

            $options = new CopyFromPOOptions(1, $userId, __METHOD__);

            /**
             *
             * @var APDoc $rootEntity ;
             */
            $rootEntity = $sv->createFromPO($source_id, $source_token, $options);

            $dto = new ApDTO();
            $dto->grDate = "2020-04-05";
            $dto->warehouse = 5;

            $options = new SaveCopyFromPOOptions(1, $userId, __METHOD__, $rootEntity);
            $cmdHandler = new SaveCopyFromPOCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecoratorTest($cmdHandler);
            $cmd = new SaveCopyFromPOCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();

            // var_dump($rootEntity);
            var_dump($dto->getErrors());
        } catch (\Exception $e) {
            echo $e->getMessage();
            echo $e->getTraceAsString();
        }
    }
}
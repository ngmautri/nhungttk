<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\GR\Options\CopyFromPOOptions;
use Procure\Application\DTO\Po\PoDTO;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Application\DTO\Gr\GrDTO;
use Procure\Application\Command\GR\Options\SaveCopyFromPOOptions;
use Procure\Application\Command\GR\SaveCopyFromPOCmdHandler;
use Procure\Application\Command\GR\SaveCopyFromPOCmd;
use Procure\Application\Command\GR\SaveCopyFromPOCmdHandlerDecorator;
use Procure\Application\Command\GR\SaveCopyFromPOCmdHandlerDecoratorTest;

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
            $sv = Bootstrap::getServiceManager()->get('Procure\Application\Service\GR\GRService');

            $source_id = 348;
            $source_token = "c19339ef-ee7a-4565-8e5f-7fecda95574d";
            $version = 1;
            $userId = 39;
            $dto = new PoDTO();

            $options = new CopyFromPOOptions(1, $userId, __METHOD__);

            /**
             *
             * @var GRDoc $rootEntity ;
             */
            $rootEntity = $sv->createFromPO($source_id, $source_token, $options);
            //var_dump($rootEntity);
         
            $dto = new GrDTO();
            $dto->grDate = "2020-04-05";
            $dto->warehouse = 5;            

            $options = new SaveCopyFromPOOptions(1, $userId, __METHOD__, $rootEntity);
            $cmdHandler = new SaveCopyFromPOCmdHandler();
            $cmdHandlerDecorator = new SaveCopyFromPOCmdHandlerDecoratorTest($cmdHandler);
            $cmd = new SaveCopyFromPOCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();

            var_dump($dto->getErrors());            
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
        }
    }
}
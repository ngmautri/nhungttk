<?php
namespace ProcureTest\GR\Command;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCmdHandlerDecoratorTest;
use Procure\Application\Command\GR\CreateHeaderCmd;
use Procure\Application\Command\GR\CreateHeaderCmdHandler;
use Procure\Application\Command\GR\Options\GrCreateOptions;
use Procure\Application\DTO\Gr\GrDTO;
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

            $companyId = 1;
            $userId = 39;

            $dto = new GrDTO();
            $dto->docCurrency = 248;
            $dto->vendor = 229;
            $dto->warehouse = 5;
            $dto->grDate = "2020-03-02";
           
            $options = new GrCreateOptions($companyId, $userId, __METHOD__);
            $cmdHandler = new CreateHeaderCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecoratorTest($cmdHandler);
            $cmd= new CreateHeaderCmd($doctrineEM, $dto, $options,$cmdHandlerDecorator);
            $cmd->execute();           

            var_dump($dto->getErrors());            
        } catch (\Exception $e) {
            echo $e->getTraceAsString();
        }
    }
}
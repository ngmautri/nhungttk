<?php
namespace ProcureTest\Ap\Rep;

use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use Procure\Application\Command\GR\PostCopyFromAPCmd;
use Procure\Application\Command\GR\PostCopyFromAPCmdHandler;
use Procure\Application\Command\GR\Options\PostCopyFromAPOptions;
use Procure\Application\DTO\Gr\GrDTO;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class QueryRepTest extends PHPUnit_Framework_TestCase
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

            $rep = new APQueryRepositoryImpl($doctrineEM);

            $id = 2828;
            $token = "3c1b51e9-f6f9-4298-946f-d58b49428571";

            $rootEntity = $rep->getRootEntityByTokenId($id, $token);

            $companyId = 1;
            $userId = 39;
            $options = new PostCopyFromAPOptions($companyId, $userId, __METHOD__, $rootEntity);

            $dto = new GrDTO();
            $cmdHandler = new PostCopyFromAPCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new PostCopyFromAPCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($dto->getNotification());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
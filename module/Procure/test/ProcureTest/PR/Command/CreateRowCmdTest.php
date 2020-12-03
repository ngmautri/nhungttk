<?php
namespace ProcureTest\PR\Command;

use Application\Infrastructure\Doctrine\CompanyQueryRepositoryImpl;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TransactionalCmdHandlerDecorator;
use Procure\Application\Command\GR\AddRowCmdHandler;
use Procure\Application\Command\GR\Options\GrRowCreateOptions;
use Procure\Application\DTO\Gr\GrRowDTO;
use Procure\Infrastructure\Doctrine\GRQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class CreateRowCmdTest extends PHPUnit_Framework_TestCase
{

    /** @var EntityManager $doctrineEM ; */
    protected $doctrineEM;

    protected $companyVO;

    public function setUp()
    {
        /** @var EntityManager $doctrineEM ; */
        $this->doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

        $rep = new CompanyQueryRepositoryImpl($doctrineEM);
        $this->companyVO = ($rep->getById(1)->createValueObject());
    }

    public function testOther()
    {
        try {

            $userId = 39;

            $data = [
                'remarks'=>'sdfsdf';
            ];

            $rootEntityId = "94";
            $rootEntityToken = "cc15908b-e12f-4403-bbda-ceb5d824f1f5";
            $version = 0;

            $rep = new GRQueryRepositoryImpl($doctrineEM);
            $rootEntity = $rep->getHeaderById($rootEntityId, $rootEntityToken);

            $options = new GrRowCreateOptions($rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);
            $cmdHandler = new AddRowCmdHandler();
            $cmdHandlerDecorator = new TransactionalCmdHandlerDecorator($cmdHandler);
            $cmd = new AddRowCmd($doctrineEM, $dto, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($dto->getErrors());
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
<?php
namespace ProcureTest\PR\Command;

use Application\Application\Command\Doctrine\GenericCommand;
use Application\Domain\Company\CompanyVO;
use Application\Infrastructure\Doctrine\CompanyQueryRepositoryImpl;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TestTransactionalCommandHandler;
use Procure\Application\Command\Doctrine\PR\UpdateRowCmdHandler;
use Procure\Application\Command\Options\UpdateRowCmdOptions;
use Procure\Application\DTO\Gr\GrRowDTO;
use Procure\Infrastructure\Doctrine\PRQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class UpdateRowCmdTest extends PHPUnit_Framework_TestCase
{

    /** @var EntityManager $doctrineEM ; */
    protected $doctrineEM;

    /**
     *
     * @var CompanyVO $companyVO ;
     */
    protected $companyVO;

    public function setUp()
    {
        /** @var EntityManager $doctrineEM ; */
        $this->doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

        $rep = new CompanyQueryRepositoryImpl($this->doctrineEM);
        $this->companyVO = ($rep->getById(1)->createValueObject());
    }

    public function testOther()
    {
        try {

            $userId = 39;

            $data = [
                'remarks' => 'test1'
            ];

            $rootEntityId = "1359";
            $rootEntityToken = "53571e71-d11d-4112-a585-d80d5ed405eb";
            $version = 18;

            $entityId = 10098;
            $entityToken = "29cee3fa-8be9-402d-bc2a-76249112189c";

            $rep = new PRQueryRepositoryImpl($this->doctrineEM);
            $rootEntity = $rep->getRootEntityByTokenId($rootEntityId, $rootEntityToken);

            $localEntity = $rootEntity->getRowbyId($entityId);

            $options = new UpdateRowCmdOptions($this->companyVO, $rootEntity, $localEntity, $entityId, $entityToken, $version, $userId, __METHOD__);
            $cmdHandler = new UpdateRowCmdHandler();
            $cmdHandlerDecorator = new TestTransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->doctrineEM, $data, $options, $cmdHandlerDecorator);
            $cmd->execute();
            // var_dump($cmd->getOutput());
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
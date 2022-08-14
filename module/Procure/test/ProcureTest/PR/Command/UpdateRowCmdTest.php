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
                'remarks' => 'test1',
                // 'edt' => '2021-09-30',
                'docQuantity' => 19
            ];

            $rootEntityId = "1436";
            $rootEntityToken = "fdcc6590-801b-4ee4-985e-caf1abefbaf7";
            $version = 37;

            $entityId = 11899;
            $entityToken = "80a77651-0f22-4b63-93d4-7666ad08ced7";

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
<?php
namespace ProcureTest\GR\Command;

use Application\Application\Command\Doctrine\GenericCommand;
use Application\Domain\Company\CompanyVO;
use Application\Infrastructure\Doctrine\CompanyQueryRepositoryImpl;
use Doctrine\ORM\EntityManager;
use ProcureTest\Bootstrap;
use Procure\Application\Command\TestTransactionalCommandHandler;
use Procure\Application\Command\Doctrine\AP\PostCmdHandler;
use Procure\Application\Command\Options\PostCmdOptions;
use Procure\Application\Eventbus\EventBusService;
use Procure\Infrastructure\Doctrine\APQueryRepositoryImpl;
use PHPUnit_Framework_TestCase;

class PostCmdTest extends PHPUnit_Framework_TestCase
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

    public function testCanNotPost()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
            $eventBusService = Bootstrap::getServiceManager()->get(EventBusService::class);

            $userId = 39;

            $rootEntityId = 3581;
            $rootEntityToken = "ab6b6dda-aeae-488b-ace7-59e6d312afca";
            $version = null;

            $rep = new APQueryRepositoryImpl($doctrineEM);
            $rootEntity = $rep->getRootEntityByTokenId($rootEntityId, $rootEntityToken);

            $options = new PostCmdOptions($this->companyVO, $rootEntity, $rootEntityId, $rootEntityToken, $version, $userId, __METHOD__);
            $cmdHandler = new PostCmdHandler();
            $cmdHandlerDecorator = new TestTransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($doctrineEM, null, $options, $cmdHandlerDecorator, $eventBusService);
            $cmd->execute();
        } catch (\Exception $e) {
            \var_dump($e->getMessage());
        }
    }
}
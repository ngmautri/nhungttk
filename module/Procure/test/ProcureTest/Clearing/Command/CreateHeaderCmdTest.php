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
use Procure\Application\Command\Doctrine\PR\CreateHeaderCmdHandler;
use Procure\Application\Command\Options\CreateHeaderCmdOptions;
use Procure\Domain\PurchaseRequest\PRSnapshot;

class CreateHeaderCmdTest extends PHPUnit_Framework_TestCase
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
                'prNumber' => 'test1',
                'submittedOn' => '2020-12-03',
                'warehouse' => '5',
                'department' => 8
            ];

            $prSnapshot = new PRSnapshot();
            $prSnapshot->department;

            $options = new CreateHeaderCmdOptions($this->companyVO, $userId, __METHOD__);
            $cmdHandler = new CreateHeaderCmdHandler();
            $cmdHandlerDecorator = new TestTransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($this->doctrineEM, $data, $options, $cmdHandlerDecorator);
            $cmd->execute();
            // var_dump($cmd->getOutput());
        } catch (\Exception $e) {
            echo $e->getMessage();
        }
    }
}
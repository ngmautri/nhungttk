<?php
namespace HRTest\Employee\Command;

use Application\Application\Command\Doctrine\GenericCommand;
use Application\Infrastructure\Doctrine\CompanyQueryRepositoryImpl;
use Doctrine\ORM\EntityManager;
use HRTest\Bootstrap;
use HR\Application\Command\TransactionalCommandHandler;
use HR\Application\Command\Doctrine\Employee\CreateIndividualCmdHandler;
use HR\Application\Command\Options\CreateIndividualCmdOptions;
use PHPUnit_Framework_TestCase;
use HR\Domain\Contracts\IndividualType;
use Application\Domain\Shared\Person\Gender;

class CreateCmdTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

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
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $userId = 39;

            $data = [

                "employeeCode" => '0654',
                "firstName" => 'Nguyen',
                "lastName" => 'Tri',
                "birthday" => "1979-01-22",
                "remarks" => "Special Item itemDescription",
                "createdBy" => $userId,
                "individualType" => IndividualType::EMPLOYEE,
                'company' => 1,
                'gender' => Gender::MALE
            ];

            $options = new CreateIndividualCmdOptions($this->companyVO, $userId, __METHOD__);

            $cmdHandler = new CreateIndividualCmdHandler();
            $cmdHandlerDecorator = new TransactionalCommandHandler($cmdHandler);
            $cmd = new GenericCommand($doctrineEM, $data, $options, $cmdHandlerDecorator);
            $cmd->execute();
            var_dump($cmdHandler->getOutput());
        } catch (\Exception $e) {
            echo $e->getMessage();
            echo "====================";
        }
    }
}
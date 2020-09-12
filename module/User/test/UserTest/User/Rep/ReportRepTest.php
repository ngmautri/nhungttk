<?php
namespace UserTest\Warehouse\Rep;

use Doctrine\ORM\EntityManager;
use UserTest\Bootstrap;
use User\Infrastructure\Persistence\Doctrine\UserReportRepositoryImpl;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class ReportRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $rep = new UserReportRepositoryImpl($doctrineEM);

            $idList = [
                39,
                47
            ];

            $users = $rep->getUserList($idList);
            var_dump($users);
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
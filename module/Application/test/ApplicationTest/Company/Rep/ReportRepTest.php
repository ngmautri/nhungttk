<?php
namespace ApplicationTest\Company\Rep;

use ApplicationTest\Bootstrap;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
use Doctrine\ORM\EntityManager;
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

            $rep = new CompanyQueryRepositoryImpl($doctrineEM);
            var_dump($rep->getById(1)->createValueObject());
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
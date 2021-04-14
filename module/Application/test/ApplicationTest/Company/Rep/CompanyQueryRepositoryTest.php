<?php
namespace ApplicationTest\Company\Rep;

use ApplicationTest\Bootstrap;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
use Application\Infrastructure\Persistence\Domain\Doctrine\Filter\CompanyQuerySqlFilter;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;

class CompanyQueryRepositoryTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
    protected $em;

    public function setUp()
    {}

    public function testOther()
    {
        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
        $rep = new CompanyQueryRepositoryImpl($em);
        $filter = new CompanyQuerySqlFilter();
        $results = $rep->getDepartmentList($filter);
        var_dump($results[1]);
    }
}
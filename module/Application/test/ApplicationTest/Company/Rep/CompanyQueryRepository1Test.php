<?php
namespace ApplicationTest\Company\Rep;

use ApplicationTest\Bootstrap;
use Application\Domain\Company\AccountChart\GenericChart;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;

class CompanyQueryRepository1Test extends PHPUnit_Framework_TestCase
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
        // $filter = new CompanyQuerySqlFilter();
        $results = $rep->getById(1);

        /**
         *
         * @var GenericChart $chart
         */
        $chart = $results->getLazyAccountChartCollection()->first();
        $root = $chart->createChartTree()->getRoot();
        echo $chart->getAccountCollection()->count();

        echo $root->display();
    }
}
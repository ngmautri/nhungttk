<?php
namespace ApplicationTest\Company\Rep;

use ApplicationTest\Bootstrap;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
use Doctrine\ORM\EntityManager;
use Inventory\Domain\Warehouse\BaseWarehouse;
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
         * @var BaseWarehouse $wh ;
         */
        $wh = $results->getLazyWarehouseCollection()->first();

        \var_dump($wh->createLocationTree()
            ->getRoot()
            ->display());

        // $root = $chart->createChartTree()->getRoot();
        // echo $root->display();
    }
}
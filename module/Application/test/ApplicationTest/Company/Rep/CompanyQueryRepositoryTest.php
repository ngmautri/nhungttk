<?php
namespace ApplicationTest\Company\Rep;

use ApplicationTest\Bootstrap;
use Application\Application\Command\Options\CmdOptions;
use Application\Application\Service\SharedServiceFactory;
use Application\Domain\Company\AccountChart\BaseAccountSnapshot;
use Application\Domain\Company\AccountChart\GenericChart;
use Application\Infrastructure\Persistence\Domain\Doctrine\CompanyQueryRepositoryImpl;
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
        // $filter = new CompanyQuerySqlFilter();
        $results = $rep->getById(1);

        /**
         *
         * @var GenericChart $chart
         */
        $chart = $results->getLazyAccountChartCollection()->first();
        $root = $chart->createChartTree()->getRoot();

        $snapshot = new BaseAccountSnapshot();
        $snapshot->setAccountNumer('111');
        $snapshot->setAccountName('Cash on Hand');

        $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
        $rep = new CompanyQueryRepositoryImpl($doctrineEM);
        // $filter = new CompanyQuerySqlFilter();
        $company = $rep->getById(1);

        $userId = 39;

        $options = new CmdOptions($company->createValueObject(), $userId, __METHOD__);
        $sharedService = SharedServiceFactory::createForCompany($em);

        $chart->createAccountFrom($snapshot, $options, $sharedService, false);

        // echo $root->display();
    }
}
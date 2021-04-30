<?php
namespace ApplicationTest\Service;

use ApplicationTest\Bootstrap;
use Application\Application\Service\Shared\DefaultFormOptionCollection;
use Application\Infrastructure\Persistence\Domain\Doctrine\Filter\CompanyQuerySqlFilter;
use Doctrine\ORM\EntityManager;
use PHPUnit_Framework_TestCase;

class DefaultFormOptionCollectionTest extends PHPUnit_Framework_TestCase
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
        try {

            /**
             *
             * @var DefaultFormOptionCollection $collections ;
             */
            $collections = Bootstrap::getServiceManager()->get(DefaultFormOptionCollection::class);
            $filter = new CompanyQuerySqlFilter();
            $filter->getCompanyId(1);

            $result = $collections->getCountryCollection($filter);
            var_dump($result);
        } catch (\Exception $e) {
            var_dump($e->getMessage());
        }
    }
}
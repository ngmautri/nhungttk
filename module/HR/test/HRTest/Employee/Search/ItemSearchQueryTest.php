<?php
namespace HRTest\Item\Search;

use Doctrine\ORM\EntityManager;
use HR\Application\Service\Search\ZendSearch\Individual\IndividualSearchQueryImpl;
use HR\Application\Service\Search\ZendSearch\Individual\Filter\IndividualQueryFilter;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class ItemSearchQueryTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $searcher = new IndividualSearchQueryImpl();
            $queryFilter = new IndividualQueryFilter();
            $results = $searcher->search("mau
*", $queryFilter);
            var_dump(($results));
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
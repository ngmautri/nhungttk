<?php
namespace InventoryTest\Item\Search;

use Doctrine\ORM\EntityManager;
use Inventory\Application\Service\Search\ZendSearch\Item\ItemSearchQueryImpl;
use Inventory\Application\Service\Search\ZendSearch\Item\Filter\ItemQueryFilter;
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
            $logger = Bootstrap::getServiceManager()->get('AppLogger');

            $searcher = new ItemSearchQueryImpl();
            $searcher->setLogger($logger);
            $queryFilter = new ItemQueryFilter();
            $results = $searcher->searchMainItem("nmt*", $queryFilter);
            var_dump(($results->getMessage()));
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
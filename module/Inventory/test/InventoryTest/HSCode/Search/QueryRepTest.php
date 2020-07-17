<?php
namespace InventoryTest\Item\Search;

use Doctrine\ORM\EntityManager;
use Inventory\Application\Service\Search\ZendSearch\HSCode\HSCodeSearchQueryImpl;
use Inventory\Application\Service\Search\ZendSearch\Item\Filter\ItemQueryFilter;
use ProcureTest\Bootstrap;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class QueryRepTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {
            /** @var EntityManager $doctrineEM ; */
            $doctrineEM = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

            $searcher = new HSCodeSearchQueryImpl();
            $queryFilter = new ItemQueryFilter();

            $hits = $searcher->search("spare*", $queryFilter);
            \var_dump($hits->getMessage());
            foreach ($hits->getHits() as $hit) {
                echo $hit->parentCode . $hit->codeDescription . "//" . $hit->codeDescription1 . "\n";
            }
        } catch (InvalidArgumentException $e) {
            var_dump($e->getMessage());
        }
    }
}
<?php
namespace InventoryTest\Model;

use PHPUnit_Framework_TestCase;
use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Domain\Exception\InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Inventory\Domain\Item\Factory\InventoryItemFactory;
use Inventory\Application\Service\Item\ItemCRUDService;
use Inventory\Domain\Item\ItemType;
use Inventory\Application\Service\Search\Solr\ItemSearchService;

class SearchSolrTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
    protected $em;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {

            $searcher = new ItemSearchService();
            
            $q="kimdfds";
            var_dump($searcher->search($q));
            
            
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
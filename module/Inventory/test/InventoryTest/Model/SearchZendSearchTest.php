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

class SearchZendSearchTest extends PHPUnit_Framework_TestCase
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

            $searcher = new \Inventory\Application\Service\Search\ZendSearch\ItemSearchService();
            
            //var_dump($searcher->optimizeIndex());
            $result = $searcher->search("juki foot*");
            var_dump($result->getMessage());
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
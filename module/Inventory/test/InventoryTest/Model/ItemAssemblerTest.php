<?php
namespace InventoryTest\Model;

use Doctrine\ORM\EntityManager;
use Inventory\Application\DTO\ItemAssembler;
use Inventory\Domain\Item\InventoryItem;
use PHPUnit_Framework_TestCase;
use Inventory\Domain\Item\Specification\ItemSpecification;
use Inventory\Domain\Item\Specification\InventoryItemSpecification;

class ItemSpecTest extends PHPUnit_Framework_TestCase
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
       
        
        $data = array();
        
        $data["itemSku"]="2-3";
        $data["itemName"]="Special Item";
        
        $itemAssembler = new ItemAssembler();
        $dto = $itemAssembler->createItemDTOFromArray($data);
        ItemAssembler::createStoreMapping();
        
        echo($dto->itemName);
        ItemAssembler::createStoreMapping();
        
       
    }
}
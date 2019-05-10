<?php
namespace InventoryTest\Model;

use PHPUnit_Framework_TestCase;
use Doctrine\ORM\EntityManager;
use InventoryTest\Bootstrap;
use Inventory\Domain\Exception\InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Inventory\Domain\Item\Repository\Doctrine\DoctrineItemRepository;
use Inventory\Domain\Item\InventoryItem;
use Inventory\Domain\Item\Specification\ItemSpecification;
use Inventory\Domain\Item\Specification\InventoryItemSpecification;
use Inventory\Domain\Item\NoneInventoryItem;
use Inventory\Domain\Item\ServiceItem;
use Inventory\Application\DTO\ItemAssembler;

class ItemAssemblerTest extends PHPUnit_Framework_TestCase
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
        //echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        $data = array();
        
        $data["itemSku"]="2-3";
        $data["itemName"]="Special Item";
         
       $itemAssembler = new ItemAssembler();
       $dto = $itemAssembler->createItemDTOFromArray($data);
       echo($dto->itemName);
    }
}
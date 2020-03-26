<?php
namespace InventoryTest\Model;

use Doctrine\ORM\EntityManager;
use Inventory\Application\DTO\Item\ItemAssembler;
use PHPUnit_Framework_TestCase;

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

        $data["itemSku"] = "2-3";
        $data["itemName"] = "Special Item";

        $itemAssembler = new \Inventory\Application\DTO\Item\ItemAssembler();
        $dto = $itemAssembler->createItemDTOFromArray($data);
        ItemAssembler::createItemDTOProperities();

        /*
         * echo($dto->itemName);
         * $missing = ItemAssembler::checkItemDTO();
         * var_dump($missing);
         */
    }
}
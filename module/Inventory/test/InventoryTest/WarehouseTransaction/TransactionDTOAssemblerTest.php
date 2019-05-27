<?php
namespace InventoryTest\WarehouseTransaction;

use Doctrine\ORM\EntityManager;
use Inventory\Application\DTO\Item\ItemAssembler;
use PHPUnit_Framework_TestCase;
use Inventory\Application\DTO\Warehouse\Transaction\WarehouseTransactionDTOAssembler;

class TransactionDTOAssembler extends PHPUnit_Framework_TestCase
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

        //WarehouseTransactionDTOAssembler::createWarehouseTransactionDTOProperities();
        //var_dump(in_array("token",WarehouseTransactionDTOAssembler::createAutoGereatedFields()));
        
        var_dump(WarehouseTransactionDTOAssembler::createAutoGereatedFields());
        
       /*  echo($dto->itemName);
        $missing = ItemAssembler::checkItemDTO();
        var_dump($missing); */
        
       
    }
}
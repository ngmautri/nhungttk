<?php
namespace InventoryTest\WarehouseTransaction;

use Inventory\Application\DTO\Warehouse\Transaction\TransactionDTOAssembler;
use PHPUnit_Framework_TestCase;

class TransactionDTOAssemblerTest extends PHPUnit_Framework_TestCase
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
        
        var_dump(TransactionDTOAssembler::createAutoGereatedFields());
        
       /*  echo($dto->itemName);
        $missing = ItemAssembler::checkItemDTO();
        var_dump($missing); */
        
       
    }
}
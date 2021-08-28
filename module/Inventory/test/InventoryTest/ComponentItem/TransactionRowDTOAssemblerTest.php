<?php
namespace InventoryTest\WarehouseTransaction;

use PHPUnit_Framework_TestCase;
use Doctrine\ORM\EntityManager;
use Inventory\Application\DTO\Warehouse\Transaction\TransactionRowDTOAssembler;
use InventoryTest\Bootstrap;

class TransactionRowDTOAssemblerTest extends PHPUnit_Framework_TestCase
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
        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');

        $data = array();

        $data["item"] = 1010;
        $data["itemSku"] = "2-3";
        $data["itemName"] = "Special Item";
        $data["docQuantity"] = "19.004";

        // WarehouseTransactionDTOAssembler::createWarehouseTransactionDTOProperities();
        // var_dump(in_array("token",WarehouseTransactionDTOAssembler::createAutoGereatedFields()));

        // var_dump(TransactionRowDTOAssembler::createDTOProperities());

        // var_dump(TransactionRowDTOAssembler::createDTOFromArray($data, $em));

        var_dump(TransactionRowDTOAssembler::createMapping());

        /*
         * echo($dto->itemName);
         * $missing = ItemAssembler::checkItemDTO();
         * var_dump($missing);
         */
    }
}
<?php
namespace InventoryTest\WarehouseTransaction;

use Doctrine\ORM\EntityManager;
use Inventory\Application\DTO\Warehouse\Transaction\TransactionDTOAssembler;
use PHPUnit_Framework_TestCase;
use Inventory\Domain\Warehouse\Transaction\TransactionType;
use InventoryTest\Bootstrap;

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
        /** @var EntityManager $em ; */
        $em = Bootstrap::getServiceManager()->get('doctrine.entitymanager.orm_default');
        
        $data = array();

        $data["itemSku"] = "2-3";
        $data["movementDate"] = "2012-12-14";
        $data["movementType"] = TransactionType::GI_FOR_COST_CENTER;
        $data["warehouse"] = 5;
        
        //WarehouseTransactionDTOAssembler::createWarehouseTransactionDTOProperities();
        //var_dump(in_array("token",WarehouseTransactionDTOAssembler::createAtoGereatedFields()));
        
        var_dump(TransactionDTOAssembler::createItemDTOFromArray($data,$em));
        
       /*  echo($dto->itemName);
        $missing = ItemAssembler::checkItemDTO();
        var_dump($missing); */
        
       
    }
}
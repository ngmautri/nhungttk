<?php
namespace InventoryTest\Warehouse;

use Doctrine\ORM\EntityManager;
use Inventory\Application\DTO\Warehouse\Transaction\TransactionDTOAssembler;
use PHPUnit_Framework_TestCase;
use Inventory\Domain\Warehouse\Transaction\TransactionType;
use InventoryTest\Bootstrap;
use Inventory\Application\DTO\Warehouse\WarehouseDTOAssembler;

class WarehouseDTOAssemblerTest extends PHPUnit_Framework_TestCase
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

        // var_dump(WarehouseDTOAssembler::findMissingProperties());
        WarehouseDTOAssembler::createStoreMapping();
    }
}
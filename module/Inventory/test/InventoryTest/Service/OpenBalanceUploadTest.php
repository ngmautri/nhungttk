<?php
namespace InventoryTest\Service;

use Doctrine\ORM\EntityManager;
use Inventory\Application\DTO\Warehouse\Transaction\TransactionDTOAssembler;
use PHPUnit_Framework_TestCase;
use Inventory\Domain\Warehouse\Transaction\TransactionType;
use InventoryTest\Bootstrap;
use Inventory\Application\DTO\Warehouse\WarehouseDTOAssembler;
use Inventory\Application\DTO\Warehouse\Location\LocationDTOAssembler;
use Inventory\Application\Service\Upload\Transaction\OpenBalanceUpload;
use PHPUnit\Framework\TestCase;

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
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        echo $root;
       
        $uploader = new OpenBalanceUpload();
        
        $file = $root . "/InventoryTest/Data/ob.xlsx";
        $uploader->doUploading($file);
        
    }
}
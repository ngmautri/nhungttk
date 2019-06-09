<?php
namespace InventoryTest\Model;

use Doctrine\ORM\EntityManager;
use Inventory\Application\DTO\Item\ItemAssembler;
use PHPUnit_Framework_TestCase;
use Inventory\Domain\Item\ItemSnapshotAssembler;

class ItemSnapshotTest extends PHPUnit_Framework_TestCase
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
        
        $data["company"]=1;
        $data["createdBy"]=39;
        $data["itemName"]="Special Item";
        
        
       var_dump(ItemSnapshotAssembler::createSnapshotFromArray($data));
        
       
    }
}
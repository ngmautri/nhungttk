<?php
namespace InventoryTest\Item;

use Inventory\Application\DTO\Item\ItemDTOAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class ItemDTOAssemblerTest extends PHPUnit_Framework_TestCase
{


    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        //echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {
            
       
            ItemDTOAssembler::createGetMapping();
           } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
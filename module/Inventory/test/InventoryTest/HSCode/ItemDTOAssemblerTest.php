<?php
namespace InventoryTest\Item;

use Inventory\Application\DTO\Item\ItemDTOAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class ItemDTOAssemblerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            $result = ItemDTOAssembler::createGetMapping();
            var_dump($result);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
<?php
namespace InventoryTest\Item;

use Application\Application\Contracts\GenericDTOAssembler;
use Application\Entity\NmtInventoryItem;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class ItemDTOAssemblerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            //$result = ItemDTOAssembler::createGetMapping();
            $result = GenericDTOAssembler::createGetMapping(NmtInventoryItem::class);

            //var_dump($result);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
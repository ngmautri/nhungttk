<?php
namespace InventoryTest\Warehouse;

use Inventory\Application\DTO\Warehouse\Location\LocationDTOAssembler;
use PHPUnit_Framework_TestCase;

class LocationDTOAssemblerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {

        // var_dump(WarehouseDTOAssembler::findMissingProperties());
        LocationDTOAssembler::createGetMapping();
    }
}
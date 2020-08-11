<?php
namespace InventoryTest\Warehouse;

use Doctrine\ORM\EntityManager;
use Inventory\Application\DTO\Warehouse\WarehouseDTOAssembler;
use PHPUnit_Framework_TestCase;

class WarehouseDTOAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
    protected $em;

    public function setUp()
    {}

    public function testOther()
    {

        // var_dump(WarehouseDTOAssembler::findMissingProperties());
        WarehouseDTOAssembler::createGetMapping();
    }
}
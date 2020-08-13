<?php
namespace InventoryTest\Warehouse;

use Inventory\Domain\Warehouse\Location\BaseLocation;
use PHPUnit_Framework_TestCase;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class LocationSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        // LocationSnapshotAssembler::findMissingDBPropsInBase();
        BaseLocation::createSnapshotBaseProps();
    }
}
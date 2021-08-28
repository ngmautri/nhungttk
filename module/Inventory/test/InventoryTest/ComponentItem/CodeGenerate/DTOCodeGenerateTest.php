<?php
namespace InventoryTest\ComponentItem\CodeGenerate;

use Inventory\Domain\Warehouse\Location\LocationSnapshot;
use Inventory\Domain\Warehouse\Location\LocationSnapshotAssembler;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class DTOCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            $result = LocationSnapshotAssembler::createFormElementsFor(LocationSnapshot::class);
            // $result = AccountSnapshotAssembler::createFormElementsFunctionFor(AccountSnapshot::class);

            // $result = GenericDTOAssembler::createStoreMapping(AppCoaAccount::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
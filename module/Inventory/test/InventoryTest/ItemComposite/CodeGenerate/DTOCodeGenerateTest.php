<?php
namespace InventoryTest\Item\CodeGenerate;

use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Inventory\Domain\Item\Composite\CompositeSnapshot;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class DTOCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {
            // $result = BrandSnapshotAssembler::createFormElementsExclude(BrandSnapshot::class);
            // $result = BrandSnapshotAssembler::createFormElementsFunctionExclude(BrandSnapshot::class);

            // $result = GenericObjectAssembler::getMethodsComments(AppCoaAccount::class);
            $result = GenericObjectAssembler::createStoreMapping(CompositeSnapshot::class);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
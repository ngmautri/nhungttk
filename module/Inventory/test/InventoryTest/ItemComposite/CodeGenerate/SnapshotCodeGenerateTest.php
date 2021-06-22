<?php
namespace InventoryTest\ItemComposite\CodeGenerate;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Inventory\Domain\Item\Composite\AbstractComposite;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class SnapshotCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {
            // $result = GenericSnapshotAssembler::findMissingProps(NmtInventoryItemComposite::class, AbstractComposite::class);
            // $result = GenericSnapshotAssembler::createSnapshotProps(GenericAttribute::class, BaseAttribute::class);
            // $result = GenericObjectAssembler::printAllFields(AbstractComposite::class, 'public');
            $result = GenericSnapshotAssembler::printAllSnapshotPropsInArrayFormat(AbstractComposite::class);
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
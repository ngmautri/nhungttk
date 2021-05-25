<?php
namespace ApplicationTest\ItemAttribute\CodeGenerate;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Application\Domain\Company\ItemAttribute\AbstractAttribute;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class SnapshotCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = GenericSnapshotAssembler::findMissingProps(NmtInventoryAttribute::class, AbstractAttribute::class);
            // $result = GenericSnapshotAssembler::findMissingProps(GenericItem::class, GenericItemSnapshot::class);

            // $result = GenericSnapshotAssembler::(AppCoa::class, AbstractChart::class);
            // $result = GenericSnapshotAssembler::findMissingProps(AppCoaAccount::class, AbstractAccount::class);

            // $result = GenericSnapshotAssembler::findMissingProps(NmtInventoryAttribute::class, AbstractAttribute::class);

            // $result = GenericSnapshotAssembler::createSnapshotProps(GenericAttribute::class, BaseAttribute::class);
            $result = GenericSnapshotAssembler::createAllSnapshotProps(AbstractAttribute::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
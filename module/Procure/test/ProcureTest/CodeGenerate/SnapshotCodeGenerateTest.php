<?php
namespace InventoryTest\ComponentItem\CodeGenerate;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Procure\Domain\RowSnapshot;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseRequest\PRRow;
use PHPUnit_Framework_TestCase;

class SnapshotCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = GenericSnapshotAssembler::findMissingProps(NmtProcurePr::class, BaseDoc::class);
            // $result = GenericSnapshotAssembler::findMissingProps(NmtProcurePoRow::class, AbstractRow::class);

            // $result = GenericSnapshotAssembler::printAllSnapshotPropsInArrayFormat(ChartSnapshot::class);
            // $result = GenericSnapshotAssembler::createAllSnapshotProps(GenericRow::class);
            $result = GenericSnapshotAssembler::createAllSnapshotPropsExclude(PRRow::class, RowSnapshot::class);

            // $result = GenericSnapshotAssembler::printAllSnapshotPropsInArrayFormat(PORow::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
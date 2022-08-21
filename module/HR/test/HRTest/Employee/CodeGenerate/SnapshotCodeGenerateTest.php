<?php
namespace InventoryTest\Item\CodeGenerate;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Application\Entity\HrIndividual;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class SnapshotCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            try {

                // $result = GenericSnapshotAssembler::findMissingProps(NmtProcurePr::class, BaseDoc::class);
                // $result = GenericSnapshotAssembler::findMissingProps(NmtProcurePrRow::class, AbstractRow::class);

                // $result = GenericSnapshotAssembler::printAllSnapshotPropsInArrayFormat(ChartSnapshot::class);
                // $result = GenericSnapshotAssembler::createAllSnapshotProps(GenericRow::class);
                // $result = GenericSnapshotAssembler::createAllSnapshotPropsExclude(PRRow::class, RowSnapshot::class);

                $result = GenericSnapshotAssembler::printAllSnapshotPropsInArrayFormat(HrIndividual::class);

                // \var_dump(($result));
            } catch (InvalidArgumentException $e) {
                echo $e->getMessage();
            }

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
<?php
namespace ProcureTest\Clearing\CodeGenerate;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Application\Entity\NmtProcureClearingDoc;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class SnapshotCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = GenericSnapshotAssembler::findMissingProps(NmtProcureClearingRow::class, ClearingRowSnapshot::class);

            $result = GenericSnapshotAssembler::createAllSnapshotProps(NmtProcureClearingDoc::class);

            // $result = GenericSnapshotAssembler::printAllSnapshotPropsInArrayFormat(BaseClearingRow::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
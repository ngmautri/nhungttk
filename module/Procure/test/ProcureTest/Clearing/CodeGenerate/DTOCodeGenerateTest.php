<?php
namespace ProcureTest\Clearing\CodeGenerate;

use Application\Application\Contracts\GenericDTOAssembler;
use Application\Entity\NmtProcureClearingRow;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class DTOCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = LocationSnapshotAssembler::createFormElementsFor(LocationSnapshot::class);
            // $result = PRSnapshotAssembler::createFormElementsFunctionFor(NmtProcurePr::class);
            // $result = PRRowSnapshotAssembler::createFormElementsFunctionFor(NmtProcurePrRow::class);

            // $result = GenericDTOAssembler::createStoreMapping(NmtProcurePrRow::class);
            $result = GenericDTOAssembler::createGetMapping(NmtProcureClearingRow::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
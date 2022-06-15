<?php
namespace InventoryTest\CodeGenerate;

use Application\Application\Contracts\GenericDTOAssembler;
use Application\Entity\NmtInventoryItemSerial;
use Application\Entity\NmtProcurePrRow;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseRequest\PRRowSnapshotAssembler;
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
            $result = PRRowSnapshotAssembler::createFormElementsFunctionFor(NmtProcurePrRow::class);

            $result = GenericDTOAssembler::createGetMapping(NmtInventoryItemSerial::class);
            // $result = GenericDTOAssembler::createStoreMapping(NmtProcurePrRow::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
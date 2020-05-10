<?php
namespace ProcureTest\PR;

use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\PORowSnapshotAssembler;
use PHPUnit_Framework_TestCase;

class PoRowSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // PORowSnapshotAssembler::findMissingPropertiesOfEntity();
            // PORow::createSnapshotProps();

            PORowSnapshotAssembler::createIndexDoc();
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
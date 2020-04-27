<?php
namespace ProcureTest;

use Procure\Domain\GenericRow;
use PHPUnit_Framework_TestCase;

class RowSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        // PORowSnapshotAssembler::findMissingPropertiesOfEntity();
        // GRRowSnapshotAssembler::findMissingPropertiesOfEntity();
        // APRowSnapshotAssembler::findMissingPropertiesOfEntity();
        GenericRow::printProps();
    }
}
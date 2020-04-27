<?php
namespace ProcureTest\GenericDoc;

use Procure\Domain\GenericDoc;
use PHPUnit_Framework_TestCase;

class RowSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        // POSnapshotAssembler::findMissingPropertiesOfEntity();
        // GRSnapshotAssembler::findMissingPropertiesOfEntity();
        // APSnapshotAssembler::findMissingPropertiesOfEntity();
        GenericDoc::printProps();
    }
}
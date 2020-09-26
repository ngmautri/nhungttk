<?php
namespace ProcureTest\GenericDoc;

use Procure\Domain\GenericDoc;
use PHPUnit_Framework_TestCase;

class DocSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        // POSnapshotAssembler::findMissingPropertiesOfEntity();
        // POSnapshotAssembler::findMissingPropsInGenericDoc();
        // APSnapshotAssembler::findMissingPropertiesOfEntity();
        GenericDoc::printProps();
    }
}
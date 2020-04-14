<?php
namespace ProcureTest;

use Procure\Domain\GenericDoc;
use PHPUnit_Framework_TestCase;

class RowSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

   

    public function setUp()
    {
        $root = realpath(dirname(dirname(__FILE__)));
        //echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {   
        //POSnapshotAssembler::findMissingPropertiesOfEntity();
        //GRSnapshotAssembler::findMissingPropertiesOfEntity();
        //APSnapshotAssembler::findMissingPropertiesOfEntity();
        GenericDoc::printProps();
    }
}
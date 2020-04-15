<?php
namespace ProcureTest;

use Procure\Domain\GenericRow;
use PHPUnit_Framework_TestCase;
use Procure\Domain\PurchaseOrder\PORowSnapshotAssembler;
use Procure\Domain\GoodsReceipt\GRRowSnapshotAssembler;
use Procure\Domain\AccountPayable\APRowSnapshotAssembler;

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
        PORowSnapshotAssembler::findMissingPropertiesOfEntity();
        //GRRowSnapshotAssembler::findMissingPropertiesOfEntity();
        //APRowSnapshotAssembler::findMissingPropertiesOfEntity();
        
       GenericRow::printProps();
    }
}
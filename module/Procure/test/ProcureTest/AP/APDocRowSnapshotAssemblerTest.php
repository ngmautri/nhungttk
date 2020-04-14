<?php
namespace ProcureTest\AP;

use Procure\Domain\APInvoice\APDocRowSnapshotAssembler;
use PHPUnit_Framework_TestCase;
use Procure\Domain\APInvoice\APDocRow;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class APDocRowSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{
  
    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        APDocRowSnapshotAssembler::findMissingPropertiesOfEntity();
        
        //APDocRow::createSnapshotProps();
        
        //APDocRowSnapshotAssembler::findMissingPropertiesOfSnapshot();
        
    }
}
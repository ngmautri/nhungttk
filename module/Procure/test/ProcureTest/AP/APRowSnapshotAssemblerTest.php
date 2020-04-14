<?php
namespace ProcureTest\AP;

use Procure\Domain\AccountPayable\APRow;
use PHPUnit_Framework_TestCase;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class APRowSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{
  
    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        //APRowSnapshotAssembler::findMissingPropertiesOfEntity();
        
        APRow::createSnapshotProps();
        
         
    }
}
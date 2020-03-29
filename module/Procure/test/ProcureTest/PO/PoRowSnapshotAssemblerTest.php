<?php
namespace ProcureTest\PR;

use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\PORowSnapshotAssembler;
use PHPUnit_Framework_TestCase;
use Procure\Domain\PurchaseOrder\PORow;

class PoRowSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

   

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        //echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {

            //PORowSnapshotAssembler::findMissingPropertiesOfEntity();
            PORow::createSnapshotProps();
            
           } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
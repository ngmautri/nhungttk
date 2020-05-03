<?php
namespace InventoryTest\Transaction;

use Inventory\Domain\Transaction\TrxDoc;
use PHPUnit_Framework_TestCase;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class TrxSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        // TrxSnapshotAssembler::findMissingPropsInEntity();
        // TrxSnapshotAssembler::findMissingPropsInGenericDoc();
        TrxDoc::createSnapshotProps();
    }
}
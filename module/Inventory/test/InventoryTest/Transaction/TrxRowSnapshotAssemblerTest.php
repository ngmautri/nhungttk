<?php

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
use Inventory\Domain\Transaction\TrxRow;

class TrxRowSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        // QrRowDTOAssembler::showEntityProperities();
        // echo '======\n';
        // TrxRowSnapshotAssembler::findMissingPropsInEntity();
        // TrxRowSnapshotAssembler::findMissingPropsInGenericRow();
        TrxRow::createSnapshotProps();
    }
}
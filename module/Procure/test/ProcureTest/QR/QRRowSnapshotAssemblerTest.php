<?php
namespace ProcureTest\QR;

use Procure\Domain\QuotationRequest\QRRow;
use PHPUnit_Framework_TestCase;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QRRowSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        // QrRowDTOAssembler::showEntityProperities();
        // echo '======\n';
        // QRRowSnapshotAssembler::findMissingPropsInEntity();
        // QRRowSnapshotAssembler::findMissingPropsInGenericRow();
        QRRow::createSnapshotProps();
    }
}
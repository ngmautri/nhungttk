<?php
namespace ProcureTest\QR;

use Procure\Domain\QuotationRequest\QRDoc;
use PHPUnit_Framework_TestCase;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QRSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        // QRSnapshotAssembler::findMissingPropsInEntity();
        // QRSnapshotAssembler::findMissingPropsInGenericDoc();
        QRDoc::createSnapshotProps();
    }
}
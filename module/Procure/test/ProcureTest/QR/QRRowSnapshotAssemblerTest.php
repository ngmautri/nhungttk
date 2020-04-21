<?php
namespace ProcureTest\QR;

use Procure\Domain\QuotationRequest\QRRowSnapshotAssembler;
use PHPUnit_Framework_TestCase;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QRRowSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        // QrRowDTOAssembler::showEntityProperities();
        echo '======\n';
        QRRowSnapshotAssembler::findMissingPropsInEntity();
        // QRRowSnapshotAssembler::findMissingPropertiesOfSnapshot();
        // QRRow::createSnapshotProps();
    }
}
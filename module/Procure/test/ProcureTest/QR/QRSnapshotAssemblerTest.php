<?php
namespace ProcureTest\QR;

use Doctrine\ORM\EntityManager;
use Procure\Domain\QuotationRequest\QRSnapshotAssembler;
use PHPUnit_Framework_TestCase;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QRSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
    protected $em;

    public function setUp()
    {
        $root = realpath(dirname(dirname(dirname(__FILE__))));
        echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        $result = QRSnapshotAssembler::findMissingPropsInEntity();
        // QRDoc::createSnapshotProps();

        // \var_dump($result);
    }
}
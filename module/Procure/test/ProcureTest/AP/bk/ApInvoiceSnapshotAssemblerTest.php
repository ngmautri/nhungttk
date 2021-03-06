<?php
namespace ProcureTest\AP;

use Doctrine\ORM\EntityManager;
use Procure\Domain\APInvoice\APDocSnapshotAssembler;
use PHPUnit_Framework_TestCase;
use Procure\Domain\APInvoice\APInvoice;

/**
 * 
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ApInvoiceSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
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
        //APDocSnapshotAssembler::findMissingPropertiesOfEntity();
        APInvoice::createSnapshotProps();
        
    }
}
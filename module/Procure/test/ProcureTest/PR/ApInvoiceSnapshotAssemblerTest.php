<?php
namespace ProcureTest\AP;

use PHPUnit_Framework_TestCase;
use Doctrine\ORM\EntityManager;
use Inventory\Domain\Warehouse\Transaction\GI\GIforRepairMachine;
use Inventory\Domain\Warehouse\WarehouseSnapshotAssembler;
use Inventory\Domain\Warehouse\Location\LocationSnapshotAssembler;
use Procure\Domain\APInvoice\APInvoiceSnapshotAssembler;

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
        APInvoiceSnapshotAssembler::createFromSnapshotCode();
    }
}
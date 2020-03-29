<?php
namespace ProcureTest\GR;

use Doctrine\ORM\EntityManager;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\POSnapshotAssembler;
use PHPUnit_Framework_TestCase;
use Procure\Domain\GoodsReceipt\GRSnapshotAssembler;
use Procure\Domain\GoodsReceipt\GRRowSnapshotAssembler;
use Procure\Domain\GoodsReceipt\GRRow;

class GrSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
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
        //echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {

             $result = GRRowSnapshotAssembler::findMissingPropertiesOfEntity();
            //$result = GRRowSnapshotAssembler::findMissingPropertiesOfSnapshot();
            $result = GRRow::createSnapshot();
            //var_dump($result);
            //var_dump($result);
            
           } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
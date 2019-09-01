<?php
namespace ProcureTest\PR;

use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\PORowSnapshotAssembler;
use PHPUnit_Framework_TestCase;

class PoRowSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
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

            PORowSnapshotAssembler::createFromDetailsSnapshotCode();
           } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
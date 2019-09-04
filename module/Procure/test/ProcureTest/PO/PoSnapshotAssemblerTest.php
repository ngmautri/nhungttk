<?php
namespace ProcureTest\PR;

use Doctrine\ORM\EntityManager;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseOrder\POSnapshotAssembler;
use PHPUnit_Framework_TestCase;

class PoSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
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

            POSnapshotAssembler::createProperities();
           } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
<?php
namespace ProcureTest\PR;

use Doctrine\ORM\EntityManager;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseRequest\PRRow;
use PHPUnit_Framework_TestCase;

class PrRowSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
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
        // echo $root;
        require ($root . '/Bootstrap.php');
    }

    public function testOther()
    {
        try {

            // PRRowSnapshotAssembler::findMissingPropsInEntity();
            // PRRowSnapshotAssembler::findMissingPropsInGenericRow();

            PRRow::createSnapshotProps();
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
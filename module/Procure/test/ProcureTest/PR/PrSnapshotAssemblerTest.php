<?php
namespace ProcureTest\PR;

use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\PurchaseRequest\PRDoc;
use Procure\Domain\PurchaseRequest\PRSnapshotAssembler;
use PHPUnit_Framework_TestCase;

class PrSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            PRSnapshotAssembler::findMissingPropsInEntity();
            PRSnapshotAssembler::findMissingPropsInGenericDoc();
            PRDoc::createSnapshotProps();
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
<?php
namespace ProcureTest\GR;

use Doctrine\ORM\EntityManager;
use Procure\Domain\Exception\InvalidArgumentException;
use Procure\Domain\GoodsReceipt\GRDoc;
use PHPUnit_Framework_TestCase;

class GrSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    /**
     *
     * @var EntityManager $em;
     */
    protected $em;

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // GRSnapshotAssembler::findMissingPropertiesOfEntity();
            // GRSnapshotAssembler::findMissingPropertiesOfSnapshot();
            GRDoc::createSnapshotProps();
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
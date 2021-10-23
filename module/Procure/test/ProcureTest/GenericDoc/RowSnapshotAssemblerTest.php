<?php
namespace ProcureTest;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Procure\Domain\BaseRow;
use PHPUnit_Framework_TestCase;

class RowSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    protected $serviceManager;

    public function setUp()
    {}

    public function testOther()
    {
        $result = GenericSnapshotAssembler::createAllSnapshotProps(BaseRow::class);
    }
}
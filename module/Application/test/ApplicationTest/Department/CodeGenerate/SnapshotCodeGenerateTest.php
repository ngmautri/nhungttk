<?php
namespace ApplicationTest\Department\CodeGenerate;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Application\Entity\NmtApplicationDepartment;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Application\Domain\Company\Department\AbstractDepartment;

class SnapshotCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = GenericSnapshotAssembler::findMissingProps(NmtInventoryItem::class, GenericItem::class);
            // $result = GenericSnapshotAssembler::findMissingProps(GenericItem::class, GenericItemSnapshot::class);

            $result = GenericSnapshotAssembler::findMissingProps(NmtApplicationDepartment::class, AbstractDepartment::class);

            // $result = GenericSnapshotAssembler::createAllSnapshotProps(UomPair::class);
            // $result = GenericSnapshotAssembler::createSnapshotProps(UomGroup::class, BaseUomGroup::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
<?php
namespace ApplicationTest\UomTest\CodeGenerate;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Application\Domain\Shared\Uom\UomGroup;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;
use Application\Entity\AppUomGroupMember;
use Application\Domain\Shared\Uom\UomPair;

class SnapshotCodeGenerateTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            // $result = GenericSnapshotAssembler::findMissingProps(AppUomGroup::class, BaseUomGroup::class);
            // $result = GenericSnapshotAssembler::findMissingProps(AppUomGroupMember::class, UomPair::class);

            // $result = GenericSnapshotAssembler::createAllSnapshotProps(UomGroup::class);
            $result = GenericSnapshotAssembler::createAllSnapshotProps(UomPair::class);
            // $result = GenericSnapshotAssembler::createSnapshotProps(UomGroup::class, BaseUomGroup::class);

            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
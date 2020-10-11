<?php
namespace ApplicationTest\oom;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Application\Domain\Shared\Uom\UomSnapshot;
use Application\Entity\NmtApplicationUom;
use Procure\Domain\Exception\InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class UomSnapshotAssemblerTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        try {

            $result = GenericSnapshotAssembler::findMissingPropsInTargetObject(NmtApplicationUom::class, UomSnapshot::class);
            //$result = GenericSnapshotAssembler::findMissingPropsInEntity(NmtApplicationUom::class,UomSnapshot::class);
            //\var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
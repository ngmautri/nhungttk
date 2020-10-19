<?php
namespace ApplicationTest\UomTest;

use Application\Application\Contracts\GenericSnapshotAssembler;
use Application\Domain\Shared\Uom\Uom;
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

            // $result = GenericSnapshotAssembler::findMissingPropsInTargetObject(NmtApplicationUom::class, BaseUom::class);
            $result = GenericSnapshotAssembler::findMissingProps(NmtApplicationUom::class, Uom::class);
            // \var_dump(($result));
        } catch (InvalidArgumentException $e) {
            echo $e->getMessage();
        }
    }
}
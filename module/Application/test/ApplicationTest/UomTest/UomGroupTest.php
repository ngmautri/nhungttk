<?php
namespace ApplicationTest\UomTest;

use PHPUnit_Framework_TestCase;
use Application\Domain\Shared\Uom\Uom;
use Application\Domain\Shared\Uom\UomGroupSnapshot;
use Application\Domain\Shared\Uom\UomGroup;

class UomGroupTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        $snapshot = new UomGroupSnapshot();
        $snapshot->setGroupName("Volumn Group");
        $snapshot->setBaseUom("each");
        \var_dump(UomGroup::createFrom($snapshot));
    }
}
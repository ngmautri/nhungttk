<?php
namespace ApplicationTest\UomTest;

use Application\Domain\Shared\Uom\QuantityUomGroup;
use Application\Domain\Shared\Uom\Uom;
use PHPUnit_Framework_TestCase;
use Application\Domain\Shared\Uom\Collection\UomGroups;

class UomFactoryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {

        $collection= new UomGroups();

        \var_dump($collection[0]);
    }

}
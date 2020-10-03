<?php
namespace ApplicationTest\uom;

use Application\Domain\Shared\Uom\Uom;
use Application\Domain\Shared\Uom\Collection\Uoms;
use PHPUnit_Framework_TestCase;
use Application\Domain\Shared\Uom\Collection\UomGroups;

class UomFactoryTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {

        //$qtyUom= new QuantityUomGroup();
        $collection = new Uoms();

        /**
         * @var Uom $um ;
         */
        \var_dump($collection->toArray());
    }

}
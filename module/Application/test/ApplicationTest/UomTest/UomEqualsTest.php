<?php
namespace ApplicationTest\UomTest;

use PHPUnit_Framework_TestCase;
use Application\Domain\Shared\Uom\Uom;

class UomEqualsTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        $oum1 = new Uom('meter', 'm');
        $other = new Uom('meter', 'm');

        \var_dump($oum1->equals($other));
    }
}
<?php
namespace ApplicationTest\Number;

use Application\Domain\Company\ValueObject\CompanyId;
use Application\Domain\Shared\Number\NumberFormatter;
use Application\Domain\Shared\Number\NumberParser;
use PHPUnit_Framework_TestCase;

class NumberTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testOther()
    {
        echo locale_get_default();

        $p = NumberParser::parse('150.000', 'la_LA');

        $p1 = NumberFormatter::format($p, 'en_EN');
        echo $p1;

        $this->expectException(\InvalidArgumentException::class);
        $id = new CompanyId(- 1);

        $id = new CompanyId(10);
    }
}
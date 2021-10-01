<?php
namespace ApplicationTest\Number;

use Application\Domain\Shared\Number\NumberFormatter;
use Application\Domain\Shared\Number\NumberParser;
use PHPUnit_Framework_TestCase;

class NumberTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testParseFailed()
    {
        $this->assertFalse(NumberParser::parse('1500001.1', 'lo_LA'));
    }

    public function testParsePass()
    {
        $p = NumberParser::parse('150000.1', 'en_EN');
        $this->assertEquals('150000.1', $p);
    }

    public function testFormatToEN()
    {
        $p = NumberParser::parseAndConvertToEN('150,000.34', 'en_EN');
        $this->assertEquals(150000.34, $p);
        echo ($p);
    }

    public function testFormatPass()
    {
        $defaultLocale = locale_get_default();
        echo $defaultLocale;

        $p1 = NumberFormatter::format('60.77', 'lo_LA');
        $this->assertEquals('60,77', $p1);
    }

    public function testNumber()
    {
        $p = NumberParser::parseAndConvertToEN('150,000.34', 'en_EN');
        echo $p;
        $this->assertTrue(\is_numeric($p));
    }

    public function testSpellOut()
    {
        $p = NumberFormatter::spellOut('60.77', 'en_EN');
        $this->assertNotFalse($p);
        echo $p;
    }
}
<?php
namespace ApplicationTest\Money;

use Application\Domain\Shared\Money\MoneyCalculator;
use Application\Domain\Shared\Money\MoneyParser;
use PHPUnit_Framework_TestCase;

class MoneyTest extends PHPUnit_Framework_TestCase
{

    public function setUp()
    {}

    public function testCanCreatePrice()
    {
        $salary = MoneyParser::parseFromLocalizedDecimal('15.000', 'lak', 'la_LA');
        // /echo (intdiv(8000, 10000));

        // var_dump($salary->subtract($sal ary));

        $a1 = MoneyParser::parseFromLocalizedDecimal('50.000', 'lak', 'la_LA');
        $a2 = MoneyParser::parseFromLocalizedDecimal('20.000', 'lak', 'la_LA');
        $a3 = MoneyParser::parseFromLocalizedDecimal('10.000', 'lak', 'la_LA');
        $a4 = MoneyParser::parseFromLocalizedDecimal('5.000', 'lak', 'la_LA');
        $a5 = MoneyParser::parseFromLocalizedDecimal('2.000', 'lak', 'la_LA');
        $a6 = MoneyParser::parseFromLocalizedDecimal('1.000', 'lak', 'la_LA');
        $r = MoneyCalculator::distributeToMoneyList($salary, [
            $a1,
            $a2,
            $a3,
            $a4,
            $a5,
            $a6
        ]);
        // var_dump($r);

        $r = MoneyCalculator::distributeToList(24, [
            29,
            3
        ]);

        var_dump($r);
    }
}
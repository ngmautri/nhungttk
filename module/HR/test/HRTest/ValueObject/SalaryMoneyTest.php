<?php
namespace HRTest\ValueObject;

use Application\Domain\Shared\Money\MoneyParser;
use HR\Domain\ValueObject\Salary\SalaryMoney;
use PHPUnit_Framework_TestCase;

/**
 * Value HR
 *
 * @author nguyen mau tri - ngmautri@gmail.com
 *
 */
class SalaryMoneyTest extends PHPUnit_Framework_TestCase
{

    private $result;

    public function testAdd()
    {
        $m = MoneyParser::parseFromLocalizedDecimal('1100000', 'lak', 'vi_VN');
        $s1 = new SalaryMoney($m);

        $m1 = MoneyParser::parseFromLocalizedDecimal('350000', 'lak', 'vi_VN');
        $s2 = new SalaryMoney($m1);

        $s3 = $s2->add($s1);
        $this->assertInstanceOf(SalaryMoney::class, $s3);

        // \var_dump($s3);
    }

    public function testSubstract()
    {
        $m = MoneyParser::parseFromLocalizedDecimal('1100000', 'lak', 'vi_VN');
        $s1 = new SalaryMoney($m);

        $m1 = MoneyParser::parseFromLocalizedDecimal('350000', 'lak', 'vi_VN');
        $s2 = new SalaryMoney($m1);

        $s3 = $s1->subtract($s2);
        $this->assertInstanceOf(SalaryMoney::class, $s3);

        // \var_dump($s3);
    }

    public function testMultiply()
    {
        $m = MoneyParser::parseFromLocalizedDecimal('1100000', 'lak', 'vi_VN');
        $s1 = new SalaryMoney($m);
        $s3 = $s1->multiply(0.5);
        $this->assertInstanceOf(SalaryMoney::class, $s3);

        // \var_dump($s3);
    }

    public function testDivide()
    {
        $m = MoneyParser::parseFromLocalizedDecimal('1100000', 'usd', 'vi_VN');
        $s1 = new SalaryMoney($m);
        $s3 = $s1->divide(5);
        $this->assertInstanceOf(SalaryMoney::class, $s3);

        \var_dump($s3);
    }

    public function testWrongCurrency()
    {
        $m = MoneyParser::parseFromLocalizedDecimal('1100000', 'lak', 'vi_VN');
        $s1 = new SalaryMoney($m);

        $m1 = MoneyParser::parseFromLocalizedDecimal('350000', 'vnd', 'vi_VN');
        $s2 = new SalaryMoney($m1);

        $this->expectException(\InvalidArgumentException::class);
        $s2->add($s1);
    }

    public function testWrongNumber()
    {
        $m = MoneyParser::parseFromLocalizedDecimal('1100000', 'lak', 'vi_VN');
        $s1 = new SalaryMoney($m);

        $m1 = MoneyParser::parseFromLocalizedDecimal('350000', 'vnd', 'vi_VN');
        $s2 = new SalaryMoney($m1);

        $this->expectException(\InvalidArgumentException::class);
        $s2->add($s1);
    }
}

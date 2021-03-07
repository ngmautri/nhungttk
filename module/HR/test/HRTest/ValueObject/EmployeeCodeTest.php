<?php
namespace HRTest\ValueObject;

use HR\Domain\ValueObject\Employee\EmployeeCode;
use PHPUnit_Framework_TestCase;

/**
 * Value hr
 *
 * @author nguyen mau tri - ngmautri@gmail.com
 *
 */
class EmployeeCodeTest extends PHPUnit_Framework_TestCase
{

    private $result;

    public function testCreateOK()
    {
        $code = new EmployeeCode('12000');
        $this->assertInstanceOf(EmployeeCode::class, $code);
    }

    public function testCreateEmptyCode()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(EmployeeCode::ERR_EMPTY);
        new EmployeeCode('');
    }

    public function testCreateCodeWithInvalidCharater()
    {
        /*
         * $this->expectException(\InvalidArgumentException::class);
         * $this->expectExceptionMessage(EmployeeCode::ERR_INVALID_CHAR);
         */
        new EmployeeCode('z10005');
    }

    public function testCreateCodeInvalidMaxLength()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(EmployeeCode::ERR_INVALID_LENGTH);
        new EmployeeCode('100000');
    }

    public function testCreateCodeInvalidMinLength()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(EmployeeCode::ERR_INVALID_LENGTH);
        new EmployeeCode('100');
        $this->getExpectedException();
    }
}
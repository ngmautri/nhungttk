<?php
namespace HRTest\ValueObject;

use HR\Domain\ValueObject\Employee\ContractDuration;
use PHPUnit_Framework_TestCase;

/**
 * Value HR
 *
 * @author nguyen mau tri - ngmautri@gmail.com
 *
 */
class ContractDurationTest extends PHPUnit_Framework_TestCase
{

    private $result;

    public function testOK()
    {
        $code = new ContractDuration('2020-11-01', '2021-11-01');
        $this->assertInstanceOf(ContractDuration::class, $code);
        \var_dump($code->getElapsedText());
        \var_dump($code->getElapsedDays());
        \var_dump($code->getRemainingText());
        \var_dump($code->getRemainingDays());
        \var_dump($code->isRunning());
        \var_dump($code->isExpired());
        \var_dump($code->notStarted());
    }

    public function testIsRunning()
    {
        $code = new ContractDuration('2020-11-01');
        $this->assertTrue($code->isRunning());
    }

    public function testIsExpired()
    {
        $code = new ContractDuration('2020-11-01', '2021-03-10');
        $this->assertTrue($code->isExpired());
    }

    public function testNotStarted()
    {
        $code = new ContractDuration('2021-11-01', '2022-11-01');
        $this->assertTrue($code->notStarted());
    }

    public function testFailed()
    {
        $this->expectException(\InvalidArgumentException::class);
        new ContractDuration('2020-12-02', '2020-11-02');
    }

    public function testEqualDate()
    {
        $code1 = new ContractDuration('2020-12-02', '2020-12-14');
        $code2 = new ContractDuration('2020-12-02', '2020-12-14');
        $this->assertTrue($code1->equals($code2));
    }
}

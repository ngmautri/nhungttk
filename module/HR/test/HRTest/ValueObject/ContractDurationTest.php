<?php
namespace HRTest\ValueObject;

use HR\Domain\ValueObject\Employee\ContractDuration;
use PHPUnit_Framework_TestCase;
use Application\Domain\Shared\Date\DateRange;

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
        $dateRange = new DateRange('2020-11-01', '2021-11-01');
        $code = new ContractDuration($dateRange);
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
        $dateRange = new DateRange('2020-11-01');
        $code = new ContractDuration($dateRange);
        $this->assertTrue($code->isRunning());
    }

    public function testIsExpired()
    {
        $dateRange = new DateRange('2020-11-01', '2021-03-10');
        $code = new ContractDuration($dateRange);
        $this->assertTrue($code->isExpired());
    }

    public function testNotStarted()
    {
        $dateRange = new DateRange('2021-11-01', '2022-11-01');
        $code = new ContractDuration($dateRange);
        $this->assertTrue($code->notStarted());
    }

    public function testFailed()
    {
        $this->expectException(\InvalidArgumentException::class);

        $dateRange = new DateRange('2020-12-02', '2020-11-02');
        new ContractDuration($dateRange);
    }

    public function testEqualDate()
    {
        $code1 = new ContractDuration(new DateRange('2020-12-02', '2020-12-14'));
        $code2 = new ContractDuration(new DateRange('2020-12-02', '2020-12-14'));

        $this->assertTrue($code1->equals($code2));
    }
}

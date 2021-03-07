<?php
namespace ApplicationTest\Person;

use Application\Domain\Shared\Date\Birthday;
use Application\Domain\Shared\Person\WorkingAge;
use PHPUnit_Framework_TestCase;

/**
 * Person Name
 *
 * @author nguyen mau tri - ngmautri@gmail.com
 *
 */
class WorkingAgeTest extends PHPUnit_Framework_TestCase
{

    private $result;

    public function testCreateOK()
    {
        $age = new WorkingAge(new Birthday('2003-03-01'), 18, 40);
        $this->assertInstanceOf(WorkingAge::class, $age);

        echo $age->getBirthday()->getAgeString();
    }

    public function testTooYoung()
    {
        $this->expectException(\InvalidArgumentException::class);
        $age = new WorkingAge(new Birthday('2003-04-01'), 18, 40);
    }

    public function testTooOld()
    {
        $this->expectException(\InvalidArgumentException::class);
        $age = new WorkingAge(new Birthday('1903-04-01'), 18, 40);
    }

    public function testEqual()
    {
        $age = new WorkingAge(new Birthday('2002-04-01'), 18, 40);
        $age1 = new WorkingAge(new Birthday('2002-04-01'), 18, 40);

        $this->assertTrue($age->equals($age1));
    }
}
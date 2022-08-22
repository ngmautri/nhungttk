<?php
namespace ApplicationTest\Date;

use Application\Domain\Shared\Date\Birthday;
use PHPUnit_Framework_TestCase;

/**
 * Person Name
 *
 * @author nguyen mau tri - ngmautri@gmail.com
 *        
 */
class BirthdayTest extends PHPUnit_Framework_TestCase
{

    private $result;

    public function testCreateOK()
    {
        $birthday = new Birthday('1950-1-1');
        $this->assertInstanceOf(Birthday::class, $birthday);

        \var_dump($birthday->getAgeYear());
        \var_dump($birthday->getAgeString());
    }

    public function testEqual()
    {
        $birthday = new Birthday('1979-01-22');
        $birthday1 = new Birthday('1979-01-22');
        $this->assertTrue($birthday->equals($birthday1));
    }
}
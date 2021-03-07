<?php
namespace ApplicationTest\Person;

use PHPUnit_Framework_TestCase;
use Application\Domain\Shared\Person\PersonName;

/**
 * Person Name
 *
 * @author nguyen mau tri - ngmautri@gmail.com
 *
 */
class PersonNameTest extends PHPUnit_Framework_TestCase
{

    private $result;

    public function testCreateOK()
    {
        $code = new PersonName('nguyen', 'mau', 'tri');
        $this->assertInstanceOf(PersonName::class, $code);
    }

    public function testWithInvalidLengthCharater()
    {
        $this->expectException(\InvalidArgumentException::class);
        $code = new PersonName('nguyen&', '', 'nguyen');
        // $this->assertInstanceOf(PersonName::class, $code);
    }

    public function testWithOnlyNumber()
    {
        $this->expectException(\InvalidArgumentException::class);

        $code = new PersonName('1123', '1234', '1234');
        // $this->assertInstanceOf(PersonName::class, $code);
    }

    public function testWithFirstNameInvalidLength()
    {
        $this->expectException(\InvalidArgumentException::class);

        $code = new PersonName('nguyen thi may tri nguyen thi may tri', 'mau', 'tri');
        // $this->assertInstanceOf(PersonName::class, $code);
    }

    public function testWithMiddleInvalidLength()
    {
        $this->expectException(\InvalidArgumentException::class);

        $code = new PersonName('nguyen', 'nguyen thi may tri nguyen thi may tri', 'Tri');
        // $this->assertInstanceOf(PersonName::class, $code);
    }

    public function testWithLastInvalidLength()
    {
        $this->expectException(\InvalidArgumentException::class);

        $code = new PersonName('nguyen', 'mau', 'nguyen thi may tri nguyen thi may tri');
        // $this->assertInstanceOf(PersonName::class, $code);
    }

    public function testEqual()
    {
        $code = new PersonName('nguyen', 'mau', 'tri');
        $code1 = new PersonName('nguyen', 'mau', 'tri');
        $this->assertTrue($code->equals($code1));
    }

    public function testNotEqual()
    {
        $code = new PersonName('nguyen', 'mau', 'tri');
        $code1 = new PersonName('nguyen', 'muu', 'tri');
        $this->assertFalse($code->equals($code1));
    }
}
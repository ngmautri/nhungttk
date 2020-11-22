<?php
namespace ApplicationTest\Email;

use Application\Domain\Shared\Email\EmailAddress;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class EmailAddressTest extends PHPUnit_Framework_TestCase
{

    public function testCanBeCreatedFromValidEmailAddress()
    {
        $this->assertInstanceOf(EmailAddress::class, EmailAddress::fromString('user@example.com'));
    }

    public function testCannotBeCreatedFromInvalidEmailAddress()
    {
        $this->expectException(InvalidArgumentException::class);

        EmailAddress::fromString('invalid');
    }

    public function testCanBeUsedAsString()
    {
        $this->assertEquals('user@example.com', EmailAddress::fromString('user@example.com'));
    }
}
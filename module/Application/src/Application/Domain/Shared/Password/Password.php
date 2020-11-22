<?php
namespace Application\Domain\Shared\Password;

use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class Password
{

    /**
     *
     * @var string Minimum required password length for customer
     */
    const MIN_LENGTH = 5;

    /**
     *
     * @var string Maximum allowed password length for customer.
     *
     *      It's limited to 72 chars because of PASSWORD_BCRYPT algorithm
     *      used in password_hash() function.
     */
    const MAX_LENGTH = 72;

    /**
     *
     * @var string
     */
    private $password;

    /**
     *
     * @param string $password
     */
    public function __construct($password)
    {
        Assert::lengthBetween($password, self::MIN_LENGTH, self::MAX_LENGTH);
        $this->password = $password;
    }

    /**
     *
     * @return string
     */
    public function getValue()
    {
        return $this->password;
    }
}

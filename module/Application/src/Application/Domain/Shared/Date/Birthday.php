<?php
namespace Application\Domain\Shared\Date;

use DateTime;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class Birthday
{

    /**
     *
     * @var string empty birthday value
     *
     *      It is used as a placeholder value when real birthday is not provided
     */
    const EMPTY_BIRTHDAY = '0000-00-00';

    /**
     *
     * @var string Date in format of Y-m-d or empty string for non defined birthday
     */
    private $birthday;

    /**
     *
     * @return Birthday
     */
    public static function createEmpty()
    {
        return new self(self::EMPTY_BIRTHDAY);
    }

    /**
     *
     * @param string $birthday
     */
    public function __construct($birthday)
    {
        $this->assertBirthdayIsInValidFormat($birthday);
        $this->assertBirthdayIsNotAFutureDate($birthday);

        $this->birthday = $birthday;
    }

    /**
     *
     * @return string
     */
    public function getValue()
    {
        return $this->birthday;
    }

    /**
     *
     * @return bool
     */
    public function isEmpty()
    {
        return self::EMPTY_BIRTHDAY === $this->birthday;
    }

    /**
     * Birthday cannot be date in a future
     *
     * @param string $birthday
     */
    private function assertBirthdayIsNotAFutureDate($birthday)
    {
        if (self::EMPTY_BIRTHDAY === $birthday) {
            return;
        }

        $birthdayDateTime = new DateTime($birthday);
        $now = new DateTime();

        if ($birthdayDateTime > $now) {
            throw new \RuntimeException(sprintf('Invalid birthday "%s" provided. Birthday must be a past date.'));
        }
    }

    /**
     * Assert that birthday is actual date
     *
     * @param string $birthday
     */
    private function assertBirthdayIsInValidFormat($birthday)
    {
        if (self::EMPTY_BIRTHDAY === $birthday) {
            return;
        }

        if (! is_string($birthday) || false === strtotime($birthday)) {
            throw new \RuntimeException(sprintf('Invalid birthday %s value provided.', var_export($birthday, true)));
        }
    }
}

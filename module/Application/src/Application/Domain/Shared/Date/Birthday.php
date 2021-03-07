<?php
namespace Application\Domain\Shared\Date;

use DateTime;
use Application\Domain\Shared\ValueObject;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class Birthday extends ValueObject
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

    private $birthdayDateTime;

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
        $this->birthdayDateTime = new DateTime($birthday);
    }

    public function makeSnapshot()
    {}

    public function getAttributesToCompare()
    {
        return [
            $this->getBirthday()
        ];
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
            throw new \InvalidArgumentException(sprintf('Invalid birthday "%s" provided. Birthday must be in the past.', $birthday));
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
            throw new \InvalidArgumentException(sprintf('Invalid birthday %s value provided.', var_export($birthday, true)));
        }
    }

    /**
     *
     * @return string
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     *
     * @return \DateTime
     */
    public function getBirthdayDateTime()
    {
        return $this->birthdayDateTime;
    }

    /**
     *
     * @return number
     */
    public function getAgeMonth()
    {
        $now = new DateTime();
        $interval = $now->diff($this->getBirthdayDateTime());
        return $interval->m;
    }

    public function getAgeYear()
    {
        $now = new DateTime();
        $interval = $now->diff($this->getBirthdayDateTime());
        return $interval->y;
    }

    public function getAgeString()
    {
        $now = new DateTime();
        $interval = $now->diff($this->getBirthdayDateTime());

        return $interval->y . " years, " . $interval->m . " months, " . $interval->d . " days ";
    }
}

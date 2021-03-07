<?php
namespace Application\Domain\Shared\Person;

use Application\Domain\Shared\ValueObject;
use Webmozart\Assert\Assert;
use Application\Domain\Util\Translator;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class PersonName extends ValueObject
{

    private $firstName;

    private $middleName;

    private $lastName;

    public function __construct($first_name, $middle_name, $last_name)
    {
        Assert::stringNotEmpty($first_name, \sprintf('%s! [%s]', Translator::translate("First name empty"), $first_name));
        Assert::stringNotEmpty($last_name, \sprintf('%s! [%s]', Translator::translate("First name empty"), $last_name));

        // $pattern = '/^[$%=-@&]*$/';
        $pattern = '/[a-zA-Z]$/'; // Contain only charater.

        Assert::regex($first_name, $pattern, \sprintf('%s! [%s]', Translator::translate("First name has invalid character"), $first_name));

        if ($middle_name != null or $middle_name != '') {
            Assert::regex($middle_name, $pattern, \sprintf('%s! [%s]', Translator::translate("Middle name has invalid character"), $middle_name));
        }

        Assert::regex($last_name, $pattern, \sprintf('%s! [%s]', Translator::translate("Last name has invalid character"), $last_name));

        Assert::maxLength($first_name, 30, \sprintf('%s! [%s]', Translator::translate("First name too long (max.30 character)"), $first_name));
        Assert::maxLength($middle_name, 30, \sprintf('%s! [%s]', Translator::translate("Middle name too long (max.30 character)"), $middle_name));
        Assert::maxLength($last_name, 30, \sprintf('%s! [%s]', Translator::translate("Last name too long (max.30 character)"), $last_name));

        $this->firstName = $first_name;
        $this->middleName = $middle_name;
        $this->lastName = $last_name;
    }

    public function makeSnapshot()
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Shared\ValueObject::getAttributesToCompare()
     */
    public function getAttributesToCompare()
    {
        return [
            $this->getFirstName(),
            $this->getMiddleName(),
            $this->getLastName()
        ];
    }

    /**
     *
     * @return mixed
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     *
     * @return mixed
     */
    public function getMiddleName()
    {
        return $this->middleName;
    }

    /**
     *
     * @return mixed
     */
    public function getLastName()
    {
        return $this->lastName;
    }
}

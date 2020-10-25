<?php
namespace Application\Domain\Shared\Person;

use Application\Domain\Shared\ValueObject;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class Name extends ValueObject
{

    private $firstName;

    private $middleName;

    private $lastName;

    public function __construct($first_name, $middle_name, $last_name)
    {
        Assert::stringNotEmpty($first_name);
        Assert::stringNotEmpty($last_name);

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

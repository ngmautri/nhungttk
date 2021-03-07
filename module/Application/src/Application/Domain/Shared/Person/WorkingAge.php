<?php
namespace Application\Domain\Shared\Person;

use Application\Domain\Shared\ValueObject;
use Application\Domain\Shared\Date\Birthday;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class WorkingAge extends ValueObject
{

    private $birthday;

    private $minAge;

    private $maxAge;

    public function __construct(Birthday $birthday, $minAge = 18, $maxAge = 65)
    {
        Assert::integer($minAge);
        Assert::integer($maxAge);
        $this->assertWorkingAge($birthday, $minAge, $maxAge);

        $this->birthday = $birthday;
        $this->minAge = $minAge;
        $this->maxAge = $maxAge;
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
        // left empty
    }

    public function equals(ValueObject $other, $caseIncentive = false)
    {
        if (! $other instanceof WorkingAge) {
            return false;
        }
        return $this->getBirthday()->equals($other->getBirthday());
    }

    /**
     *
     * @return \Application\Domain\Shared\Date\Birthday
     */
    public function getBirthday()
    {
        return $this->birthday;
    }

    /**
     *
     * @return number
     */
    public function getMinAge()
    {
        return $this->minAge;
    }

    /**
     *
     * @return number
     */
    public function getMaxAge()
    {
        return $this->maxAge;
    }

    private function assertWorkingAge(Birthday $birthday, $minAge, $maxAge)
    {
        if ($minAge >= $maxAge) {
            throw new \InvalidArgumentException(sprintf('Invalid working age condition! [%s >= %s]', $minAge, $maxAge));
        }

        $age = $birthday->getAgeYear();

        if ($age < $minAge) {
            throw new \InvalidArgumentException(sprintf('Invalid working age! Too young.[Birthday:%s; %s]', $birthday->getBirthday(), $birthday->getAgeString()));
        }

        if ($age >= $maxAge) {
            throw new \InvalidArgumentException(sprintf('Invalid working age! Too old.[Birthday:%s; %s]', $birthday->getBirthday(), $birthday->getAgeString()));
        }
    }
}

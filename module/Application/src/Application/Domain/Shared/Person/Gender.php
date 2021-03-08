<?php
namespace Application\Domain\Shared\Person;

use Application\Domain\Shared\ValueObject;
use Application\Domain\Util\Translator;
use Webmozart\Assert\Assert;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class Gender extends ValueObject
{

    const MALE = 'M';

    const FEMALE = 'F';

    const OTHER = 'O';

    private $gender;

    public function __construct($gender)
    {
        Assert::stringNotEmpty($gender, \sprintf('%s! [%s]', Translator::translate("Gender not set!"), $gender));
        if (! in_array($gender, self::getSupportedType())) {
            throw new InvalidArgumentException(\sprintf('%s! [%s]', Translator::translate("Gender in not valid"), $gender));
        }

        $this->gender = $gender;
    }

    public function makeSnapshot()
    {}

    public function getAttributesToCompare()
    {
        return [
            $this->getGender()
        ];
    }

    public static function getSupportedType()
    {
        $r = [];
        $r[] = self::MALE;
        $r[] = self::FEMALE;
        $r[] = self::OTHER;
        return $r;
    }

    /**
     *
     * @return mixed
     */
    public function getGender()
    {
        return $this->gender;
    }
}

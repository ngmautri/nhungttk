<?php
namespace HR\Domain\ValueObject\Employee;

use Application\Domain\Shared\ValueObject;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
final class EmployeeCode extends ValueObject
{

    const ERR_INVALID_LENGTH = "Length of code must be 4-5 charater";

    const ERR_EMPTY = "Employee code is empty";

    const ERR_INVALID_CHAR = "Employee code has invalid charater";

    private $code;

    public function __construct($code)
    {
        Assert::stringNotEmpty($code, self::ERR_EMPTY);

        $pattern = '/^[0-9]*$/';
        Assert::regex($code, $pattern, \sprintf('%s! [%s]', self::ERR_INVALID_CHAR, $code));

        Assert::lengthBetween($code, 4, 5, self::ERR_INVALID_LENGTH);
        $this->code = $code;
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
            $this->getCode()
        ];
    }

    /**
     *
     * @return mixed
     */
    public function getCode()
    {
        return $this->code;
    }
}

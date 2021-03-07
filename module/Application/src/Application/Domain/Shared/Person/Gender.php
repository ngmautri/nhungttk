<?php
namespace Application\Domain\Shared\Person;

use Application\Domain\Shared\ValueObject;

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

    public function makeSnapshot()
    {}

    public function getAttributesToCompare()
    {}
}

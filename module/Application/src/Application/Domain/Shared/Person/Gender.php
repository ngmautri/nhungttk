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

    const MALE = 'male';

    const FEMALE = 'female';

    const OTHER = 'other';

    public function makeSnapshot()
    {}

    public function getAttributesToCompare()
    {}
}

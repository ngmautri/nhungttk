<?php
namespace HR\Domain\Contracts;

/**
 * Document Status
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class IndividualType

{

    const APPLICANT = 0;

    const EMPLOYEE = 1;

    public static function getSupportedType()
    {
        $r = [];
        $r[] = self::APPLICANT;
        $r[] = self::EMPLOYEE;
        return $r;
    }
}
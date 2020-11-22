<?php
namespace Application\Domain\Shared\Number;

/**
 *
 * @author nguyen mau tri - ngmautri@gmail.com
 *
 */
final class NumberParser
{

    public static function parse($number, $local = 'en_EN')
    {
        $numberFormatter = new \NumberFormatter($local, \NumberFormatter::DECIMAL);
        return $numberFormatter->parse($number);
    }
}

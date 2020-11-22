<?php
namespace Application\Domain\Shared\Number;

/**
 *
 * @author nguyen mau tri - ngmautri@gmail.com
 *
 */
final class NumberFormatter
{

    public static function format($number, $local = 'en_EN')
    {
        $numberFormatter = new \NumberFormatter($local, \NumberFormatter::DECIMAL);
        return $numberFormatter->format($number);
    }
}

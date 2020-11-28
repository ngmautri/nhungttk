<?php
namespace Application\Domain\Shared\Number;

/**
 *
 * @author nguyen mau tri - ngmautri@gmail.com
 *
 */
final class NumberFormatter
{

    public static function format($number, $local = 'en_EN', $minFractionDigits = 2, $maxFractionDigits = 2)
    {
        $numberFormatter = new \NumberFormatter($local, \NumberFormatter::DECIMAL);
        $numberFormatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $minFractionDigits);
        $numberFormatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $maxFractionDigits);
        return $numberFormatter->format($number);
    }

    public static function formatToEN($number)
    {
        $numberFormatter = new \NumberFormatter('en_EN', \NumberFormatter::DECIMAL);
        return $numberFormatter->format($number);
    }

    public static function spellOutInEnglish($number)
    {
        return self::spellOut($number);
    }

    public static function spellOut($number, $local = 'en_EN')
    {
        $numberFormatter = new \NumberFormatter($local, \NumberFormatter::SPELLOUT);
        return $numberFormatter->format($number);
    }
}

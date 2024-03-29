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

    public static function formatMoneyNumberForExcel($number, $currency, $local = 'en_EN', $minFractionDigits = 0, $maxFractionDigits = 0)
    {
        if ($number == 0 or $number == null) {
            return 0;
        }
        return self::_formatMoneyNumber($number, $currency, $minFractionDigits, $maxFractionDigits);
    }

    public static function formatMoneyNumberForGrid($number, $currency, $local = 'en_EN', $minFractionDigits = 0, $maxFractionDigits = 0)
    {
        if ($number == 0 or $number == null) {
            return '<span style="color: white;">0</span>';
        }

        return self::_formatMoneyNumber($number, $currency, $minFractionDigits, $maxFractionDigits);
    }

    public static function formatNumberForGrid($number, $local = 'en_EN', $minFractionDigits = 2, $maxFractionDigits = 2)
    {
        if ($number == 0 or $number == null) {
            return '<span style="color: white;">0</span>';
        }
        return self::_formatNumber($number, $local, $minFractionDigits, $maxFractionDigits);
    }

    public static function formatNumberForExcel($number, $local = 'en_EN', $minFractionDigits = 2, $maxFractionDigits = 2)
    {
        if ($number == 0 or $number == null) {
            return 0;
        }

        return self::_formatNumber($number, $local, $minFractionDigits, $maxFractionDigits);
    }

    private static function _formatMoneyNumber($number, $currency, $local = 'en_EN', $minFractionDigits = 0, $maxFractionDigits = 0)
    {
        $curencyList = array(
            "USD",
            "THB",
            "EUR"
        );

        if (in_array($currency, $curencyList)) {
            $minFractionDigits = 2;
            $maxFractionDigits = 2;
        }

        $numberFormatter = new \NumberFormatter($local, \NumberFormatter::DECIMAL);
        $numberFormatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $minFractionDigits);
        $numberFormatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $maxFractionDigits);
        return $numberFormatter->format($number);
    }

    private static function _formatNumber($number, $local = 'en_EN', $minFractionDigits = 0, $maxFractionDigits = 0)
    {
        $numberFormatter = new \NumberFormatter($local, \NumberFormatter::DECIMAL);
        $numberFormatter->setAttribute(\NumberFormatter::MIN_FRACTION_DIGITS, $minFractionDigits);
        $numberFormatter->setAttribute(\NumberFormatter::MAX_FRACTION_DIGITS, $maxFractionDigits);
        return $numberFormatter->format($number);
    }
}

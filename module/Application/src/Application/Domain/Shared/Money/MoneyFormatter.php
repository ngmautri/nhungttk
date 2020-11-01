<?php
namespace Application\Domain\Shared\Money;

use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Formatter\IntlMoneyFormatter;

/**
 *
 * @author nguyen mau tri - ngmautri@gmail.com
 *
 */
final class MoneyFormatter
{

    public static function format($money, $local = 'en_EN')
    {
        $currencies = new ISOCurrencies();
        $numberFormatter = new \NumberFormatter($local, \NumberFormatter::DECIMAL);
        $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);
        return $moneyFormatter->format($money);
    }

    public static function formatDecimal($money)
    {
        $currencies = new ISOCurrencies();
        $moneyFormatter = new DecimalMoneyFormatter($currencies);
        return $moneyFormatter->format($money); // outputs 1.00
    }
}

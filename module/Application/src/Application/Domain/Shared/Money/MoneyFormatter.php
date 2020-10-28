<?php
namespace Application\Domain\Shared\Money;

use Money\Currencies\ISOCurrencies;
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
}

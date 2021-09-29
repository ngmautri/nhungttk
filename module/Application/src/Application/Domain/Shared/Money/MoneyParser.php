<?php
namespace Application\Domain\Shared\Money;

use Money\Currency;
use Money\Currencies\ISOCurrencies;
use Money\Parser\DecimalMoneyParser;
use Money\Parser\IntlLocalizedDecimalParser;

/**
 *
 * @author nguyen mau tri - ngmautri@gmail.com
 *        
 */
final class MoneyParser
{

    public static function parseFromDecimal($amount, $currency)
    {
        $currencies = new ISOCurrencies();
        $moneyParser = new DecimalMoneyParser($currencies);
        return $moneyParser->parse($amount, new Currency(strtoupper($currency)));
    }

    public static function parseFromLocalizedDecimal($amount, $currency, $local = "en_EN")
    {
        $currencies = new ISOCurrencies();
        $numberFormatter = new \NumberFormatter($local, \NumberFormatter::DECIMAL);
        $moneyParser = new IntlLocalizedDecimalParser($numberFormatter, $currencies);

        return $moneyParser->parse($amount, new Currency(strtoupper($currency)));
    }
}

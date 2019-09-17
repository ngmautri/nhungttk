<?php
namespace Application\Domain\Shared;

use Money\Currency;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class Money extends AbstractValueObject
{

    /**
     *
     * @var int
     */
    private $amount;

    /**
     *
     * @var Currency
     */
    private $currency;

    /**
     *
     * @var Currency
     */
    private $exchangeRate;

    /**
     *
     * @param int $amount
     * @param Currency $currency
     * @var Currency
     */
    function __construct($amount, Currency $currency, $exchangeRate)
    {
        $this->amount = $amount;
        $this->currency = $currency;
        $this->exchangeRate = $exchangeRate;
    }
}

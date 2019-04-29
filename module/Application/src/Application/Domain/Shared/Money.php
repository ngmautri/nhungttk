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
     * @param int $amount
     * @param Currency $currency
     * @var Currency
     */
    function __construct(int $amount, Currency $currency)
    {
        $this->amount = $amount;
        $this->currency = $currency;
    }
}

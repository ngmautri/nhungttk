<?php
namespace Application\Domain\Shared\Money;

use Money\Money;

/**
 *
 * @author nguyen mau tri - ngmautri@gmail.com
 *        
 */
final class MoneyCalculator
{

    public static function distributeToMoneyList(Money $amount, array $list)
    {
        $results = [];

        foreach ($list as $k => $v) {

            if (! $v instanceof Money) {
                throw new \InvalidArgumentException('expected Money Object');
            }

            $n = intdiv($amount->getAmount(), $v->getAmount());
            $remain = ($amount->getAmount() % $v->getAmount());
            $result[$k] = [
                $v,
                $n
            ];

            $remainMoney = new Money($remain, $v->getCurrency());
            $amount = $remainMoney;
        }

        return $result;
    }

    public static function distributeToList($amount, array $list)
    {
        $results = [];
        $remain = $amount;

        foreach ($list as $k => $v) {

            $tmp = $amount - $v;

            if ($tmp > 0) {
                $n = $v;
                $amount = $tmp;
                $remain = $tmp;
            } else {
                $n = $amount;
                $amount = 0;
                $remain = 0;
            }

            $result[$k] = [
                $v,
                $n
            ];
        }

        return [
            '1' => $result,
            '2' => $remain
        ];
    }
}

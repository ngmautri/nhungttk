<?php
namespace Application\Domain\Shared\Quantity\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface Calculator
{

    public static function supported();

    public function compare($a, $b);

    public function multiply($amount, $multiplier);
}

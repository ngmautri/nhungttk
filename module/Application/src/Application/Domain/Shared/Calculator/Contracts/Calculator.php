<?php
namespace Application\Domain\Shared\Calculator\Contracts;

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

    public function add($amount, $addend);

    public function subtract($amount, $subtrahend);

    public function divide($amount, $divisor);

}

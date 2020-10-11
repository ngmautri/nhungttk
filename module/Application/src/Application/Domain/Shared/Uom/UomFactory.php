<?php
namespace Application\Domain\Shared\Uom;

/**
 * This is a automatically generated file.
 *
 * @method static Uom BAG()
 * @method static Uom BOTTLE()
 * @method static Uom BOX()
 * @method static Uom BUNDLE()
 * @method static Uom CARTON()
 * @method static Uom CENTIMETER()
 * @method static Uom CUBICMETER()
 * @method static Uom DOZEN()
 * @method static Uom EACH()
 * @method static Uom FEET()
 * @method static Uom GALLON()
 * @method static Uom GRAM()
 * @method static Uom HOUR()
 * @method static Uom INCH()
 * @method static Uom KILOGRAM()
 * @method static Uom LITER()
 * @method static Uom METER()
 * @method static Uom MILILITER()
 * @method static Uom MINUTE()
 * @method static Uom PACK()
 * @method static Uom PAIR()
 * @method static Uom PIECES()
 * @method static Uom POUND()
 * @method static Uom SECOND()
 * @method static Uom SET()
 * @method static Uom TANK()
 * @method static Uom TON()
 * @method static Uom YARD()
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 */
trait UomFactory
{

    /**
     *
     * @param string $method
     * @param string $arguments
     * @return Uom
     *
     * @throws \InvalidArgumentException If amount is not integer(ish)
     */
    public static function __callStatic($method, $arguments)
    {
        return new Uom($method);
    }
}
